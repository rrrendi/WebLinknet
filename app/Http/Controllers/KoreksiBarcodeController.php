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
            ->map(function($activity) use ($user) {
                return [
                    'id' => $activity->id,
                    'aktivitas' => $activity->aktivitas,
                    'tanggal' => $activity->tanggal->format('d-m-Y H:i:s'),
                    'result' => $activity->result,
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
                    'message' => 'Anda tidak memiliki izin untuk menghapus aktivitas ini!'
                ], 403);
            }

            $detail = $activity->igiDetail;
            $aktivitasType = $activity->aktivitas;

            // Validasi: hanya bisa hapus jika ini aktivitas terakhir
            if ($detail->status_proses !== $aktivitasType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa hapus! Ini bukan aktivitas terakhir. Status barang saat ini: ' . $detail->status_proses
                ], 403);
            }

            // Hapus record terkait berdasarkan aktivitas
            switch ($aktivitasType) {
                case 'UJI_FUNGSI':
                    $detail->ujiFungsi()->where('user_id', $activity->user_id)->delete();
                    $detail->updateStatusProses('IGI');
                    break;
                case 'REPAIR':
                    $detail->repair()->where('user_id', $activity->user_id)->delete();
                    $detail->updateStatusProses('UJI_FUNGSI');
                    break;
                case 'REKONDISI':
                    $detail->rekondisi()->where('user_id', $activity->user_id)->delete();
                    // Kembali ke status sebelumnya
                    $previousStatus = $detail->getPreviousStatus();
                    $detail->updateStatusProses($previousStatus);
                    break;
                case 'SERVICE_HANDLING':
                    $detail->serviceHandling()->where('user_id', $activity->user_id)->delete();
                    $previousStatus = $detail->getPreviousStatus();
                    $detail->updateStatusProses($previousStatus);
                    break;
                case 'PACKING':
                    $detail->packing()->where('user_id', $activity->user_id)->delete();
                    $previousStatus = $detail->getPreviousStatus();
                    $detail->updateStatusProses($previousStatus);
                    break;
            }

            // Hapus activity log
            $activity->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}