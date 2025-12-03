<?php

namespace App\Http\Controllers;

use App\Models\Igi;
use App\Models\KoreksiBarcode;
use App\Models\UjiFungsi;
use App\Models\Repair;
use App\Models\Rekondisi;
use App\Models\ServiceHandling;
use App\Models\Packing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KoreksiBarcodeController extends Controller
{
    public function index()
    {
        $history = KoreksiBarcode::with(['user', 'igi'])
            ->orderBy('tanggal_koreksi', 'desc')
            ->paginate(20);
        
        return view('koreksi-barcode.index', compact('history'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'serial_number' => 'required'
        ], [
            'serial_number.required' => 'Serial Number wajib diisi'
        ]);

        $igi = Igi::with('master')->where('serial_number', $request->serial_number)->first();

        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan dalam database IGI'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $igi->id,
                'no_do' => $igi->no_do,
                'tanggal_datang' => $igi->tanggal_datang,
                'nama_barang' => $igi->nama_barang,
                'type' => $igi->type,
                'serial_number' => $igi->serial_number,
                'status_proses' => $igi->status_proses
            ]
        ]);
    }

    public function tracking($serialNumber)
    {
        $igi = Igi::where('serial_number', $serialNumber)->first();
        
        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan'
            ], 404);
        }

        $tracking = [
            'uji_fungsi' => $igi->ujiFungsi()->orderBy('created_at', 'desc')->first(),
            'repair' => $igi->repair()->orderBy('created_at', 'desc')->first(),
            'rekondisi' => $igi->rekondisi()->orderBy('created_at', 'desc')->first(),
            'service_handling' => $igi->serviceHandling()->orderBy('created_at', 'desc')->first(),
            'packing' => $igi->packing()->orderBy('created_at', 'desc')->first(),
        ];

        return response()->json($tracking);
    }

    public function updateData(Request $request)
    {
        $request->validate([
            'igi_id' => 'required|exists:igi,id',
            'nama_barang' => 'required|in:ONT,STB,ROUTER',
            'type' => 'required|string|max:255'
        ], [
            'igi_id.required' => 'IGI ID wajib diisi',
            'igi_id.exists' => 'Data IGI tidak ditemukan',
            'nama_barang.required' => 'Nama Barang wajib diisi',
            'nama_barang.in' => 'Nama Barang harus ONT, STB, atau ROUTER',
            'type.required' => 'Type wajib diisi'
        ]);

        DB::beginTransaction();
        try {
            $igi = Igi::with('master')->findOrFail($request->igi_id);
            
            // Simpan data lama untuk log
            $namaBarangLama = $igi->nama_barang;
            $typeLama = $igi->type;
            
            // Cek apakah ada perubahan
            if ($namaBarangLama === $request->nama_barang && $typeLama === $request->type) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada perubahan data'
                ], 400);
            }

            // Simpan log koreksi
            KoreksiBarcode::create([
                'igi_id' => $igi->id,
                'nama_barang_lama' => $namaBarangLama,
                'nama_barang_baru' => $request->nama_barang,
                'type_lama' => $typeLama,
                'type_baru' => $request->type,
                'tanggal_koreksi' => Carbon::now(),
                'user_id' => Auth::id()
            ]);

            // Update di IGI Operasional
            $igi->update([
                'nama_barang' => $request->nama_barang,
                'type' => $request->type
            ]);

            // Update di IGI Master
            $igi->master->update([
                'nama_barang' => $request->nama_barang,
                'type' => $request->type
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data barang berhasil diperbarui di Master & Operasional'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $query = KoreksiBarcode::with(['user', 'igi']);

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal_koreksi', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal_koreksi', '<=', $request->end_date);
        }

        $history = $query->orderBy('tanggal_koreksi', 'desc')->paginate(20);
        
        return view('koreksi-barcode.history', compact('history'));
    }
}