<?php
// app/Http/Controllers/KoreksiBarcodeController.php
namespace App\Http\Controllers;

use App\Models\IgiDetail;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KoreksiBarcodeController extends Controller
{
    public function index()
    {
        return view('koreksi-barcode.index');
    }

    // Search Serial Number
    public function search(Request $request)
    {
        $request->validate([
            'serial_number' => 'required'
        ]);

        $detail = IgiDetail::with(['bapb', 'scanner'])
            ->where('serial_number', $request->serial_number)
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $detail->id,
                'pemilik' => $detail->bapb->pemilik,
                'wilayah' => $detail->bapb->wilayah,
                'tanggal_datang' => $detail->bapb->tanggal_datang->format('d-m-Y'),
                'serial_number' => $detail->serial_number,
                'mac_address' => $detail->mac_address,
                'stb_id' => $detail->stb_id,
                'jenis' => $detail->jenis,
                'merk' => $detail->merk,
                'type' => $detail->type,
            ]
        ]);
    }

    // Get Activity History
    public function getActivityHistory(Request $request, $id)
    {
        $detail = IgiDetail::findOrFail($id);
        $user = $request->user();

        $activities = $detail->activityLogs()
            ->with('user')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($activity) use ($user) {

                $resultLabel = $activity->result;
                if ($activity->result === 'N/A') {
                    // Untuk aktivitas yang tidak punya result, beri label yang lebih deskriptif
                    switch ($activity->aktivitas) {
                        case 'IGI':
                            $resultLabel = 'MASUK';
                            break;
                        case 'REKONDISI':
                            $resultLabel = 'SELESAI';
                            break;
                        case 'PACKING':
                            $resultLabel = 'DIKEMAS';
                            break;
                        case 'KOREKSI':
                            $resultLabel = 'DIKOREKSI';
                            break;
                        default:
                            $resultLabel = 'RUSAK'; // Checkmark saja
                    }
                }

                return [
                    'id' => $activity->id,
                    'aktivitas' => $activity->aktivitas,
                    'tanggal' => $activity->tanggal->format('d-m-Y H:i:s'),
                    'result' => $activity->result,
                    'result_label' => $resultLabel,
                    'user_name' => $activity->user->name,
                    'user_id' => $activity->user_id,
                    'can_delete' => $user?->canDeleteActivity($activity->user_id) ?? false,
                    'keterangan' => $activity->keterangan
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    // Update Data (Koreksi)
    public function update(Request $request, $id)
    {
        $request->validate([
            'mac_address' => 'required|string',
            'jenis' => 'required|in:STB,ONT,ROUTER',
            'merk' => 'required|string',
            'type' => 'required|string',
            'stb_id' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $detail = IgiDetail::findOrFail($id);

            // Validasi STB ID: wajib untuk STB, harus kosong untuk selain STB
            if ($request->jenis === 'STB' && empty($request->stb_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'STB ID wajib diisi untuk jenis STB!'
                ], 400);
            }

            // Simpan data lama untuk log
            $dataLama = [
                'mac_address' => $detail->mac_address,
                'jenis' => $detail->jenis,
                'merk' => $detail->merk,
                'type' => $detail->type,
                'stb_id' => $detail->stb_id
            ];

            $dataBaru = [
                'mac_address' => $request->mac_address,
                'jenis' => $request->jenis,
                'merk' => $request->merk,
                'type' => $request->type,
                'stb_id' => $request->jenis === 'STB' ? $request->stb_id : null
            ];

            // Check apakah ada perubahan
            if ($dataLama == $dataBaru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada perubahan data!'
                ], 400);
            }

            // Update data
            $detail->update($dataBaru);

            // Log koreksi
            ActivityLog::create([
                'igi_detail_id' => $detail->id,
                'aktivitas' => 'KOREKSI',
                'tanggal' => now(),
                'result' => 'N/A',
                'user_id' => Auth::id(),
                'keterangan' => 'Koreksi data barang',
                'data_lama' => $dataLama,
                'data_baru' => $dataBaru
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete Activity (hanya yang membuat)
    // Delete Activity (hanya yang membuat)
    public function deleteActivity(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $activity = ActivityLog::with('igiDetail')->findOrFail($id);

            // Check permission
            if (!$user || !$user->canDeleteActivity($activity->user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus aktivitas ini!',
                    'error_type' => 'PERMISSION_DENIED',
                    'detail' => 'Hanya pembuat aktivitas atau Admin yang dapat menghapus aktivitas.'
                ], 403);
            }

            $detail = $activity->igiDetail;
            $aktivitasType = $activity->aktivitas;

            // Validasi: hanya bisa hapus jika ini aktivitas terakhir
            if ($detail->status_proses !== $aktivitasType) {
                // Cari tahu aktivitas apa yang ada setelahnya
                $nextActivities = $detail->activityLogs()
                    ->where('tanggal', '>', $activity->tanggal)
                    ->pluck('aktivitas')
                    ->unique()
                    ->implode(', ');

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa menghapus aktivitas ini!',
                    'error_type' => 'NOT_LAST_ACTIVITY',
                    'detail' => sprintf(
                        'Aktivitas "%s" bukan aktivitas terakhir. Status barang saat ini: "%s". Aktivitas setelahnya: %s',
                        $aktivitasType,
                        $detail->status_proses,
                        $nextActivities ?: 'tidak ada'
                    )
                ], 403);
            }

            // Validasi khusus: tidak bisa hapus IGI
            if ($aktivitasType === 'IGI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus aktivitas IGI!',
                    'error_type' => 'CANNOT_DELETE_IGI',
                    'detail' => 'Aktivitas IGI adalah aktivitas awal yang tidak dapat dihapus. Hubungi Admin jika ada masalah.'
                ], 403);
            }

            // Hapus record terkait berdasarkan aktivitas
            switch ($aktivitasType) {
                case 'UJI_FUNGSI':
                    $deleted = $detail->ujiFungsi()->where('user_id', $activity->user_id)->delete();
                    if ($deleted > 0) {
                        $detail->updateStatusProses('IGI');
                    }
                    break;
                case 'REPAIR':
                    $deleted = $detail->repair()->where('user_id', $activity->user_id)->delete();
                    if ($deleted > 0) {
                        $detail->updateStatusProses('UJI_FUNGSI');
                    }
                    break;
                case 'REKONDISI':
                    $deleted = $detail->rekondisi()->where('user_id', $activity->user_id)->delete();
                    if ($deleted > 0) {
                        $previousStatus = $detail->getPreviousStatus();
                        $detail->updateStatusProses($previousStatus);
                    }
                    break;
                case 'SERVICE_HANDLING':
                    $deleted = $detail->serviceHandling()->where('user_id', $activity->user_id)->delete();
                    if ($deleted > 0) {
                        $previousStatus = $detail->getPreviousStatus();
                        $detail->updateStatusProses($previousStatus);
                    }
                    break;
                case 'PACKING':
                    $deleted = $detail->packing()->where('user_id', $activity->user_id)->delete();
                    if ($deleted > 0) {
                        $previousStatus = $detail->getPreviousStatus();
                        $detail->updateStatusProses($previousStatus);
                    }
                    break;
                case 'KOREKSI':
                    // Koreksi tidak mengubah status, langsung hapus saja
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Jenis aktivitas tidak dikenali!',
                        'error_type' => 'UNKNOWN_ACTIVITY',
                        'detail' => sprintf('Aktivitas "%s" tidak dapat dihapus melalui menu ini.', $aktivitasType)
                    ], 400);
            }

            // Hapus activity log
            $activity->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => sprintf('Aktivitas "%s" berhasil dihapus!', $aktivitasType),
                'new_status' => $detail->fresh()->status_proses
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan!',
                'error_type' => 'NOT_FOUND',
                'detail' => 'Aktivitas mungkin sudah dihapus sebelumnya.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem!',
                'error_type' => 'SERVER_ERROR',
                'detail' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
