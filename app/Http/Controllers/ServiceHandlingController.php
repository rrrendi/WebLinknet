<?php

namespace App\Http\Controllers;

use App\Models\ServiceHandling;
use App\Models\UjiFungsi;
use App\Models\Repair;
use App\Models\Igi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceHandlingController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoringData();
        $recentService = ServiceHandling::with('igi')->orderBy('waktu_service', 'desc')->paginate(10);
        
        return view('service-handling.index', compact('monitoring', 'recentService'));
    }

    public function getMonitoring()
    {
        $monitoring = $this->getMonitoringData();
        return response()->json($monitoring);
    }

    private function getMonitoringData()
    {
        $categories = ['ONT', 'STB', 'ROUTER'];
        $monitoring = [];

        foreach ($categories as $category) {
            $count = ServiceHandling::whereHas('igi', function($query) use ($category) {
                $query->where('nama_barang', $category);
            })->count();
            
            $monitoring[$category] = $count;
        }

        $monitoring['TOTAL'] = array_sum($monitoring);
        return $monitoring;
    }

    public function getNokData(Request $request)
    {
        // Get all NOK items from Uji Fungsi and Repair
        $ujiFungsiNok = UjiFungsi::with('igi')
            ->where('status', 'NOK')
            ->get()
            ->map(function($item) {
                return [
                    'igi_id' => $item->igi_id,
                    'serial_number' => $item->igi->serial_number,
                    'nama_barang' => $item->igi->nama_barang,
                    'type' => $item->igi->type,
                    'status' => $item->status,
                    'sumber' => 'UJI_FUNGSI',
                    'timestamp' => $item->waktu_uji
                ];
            });

        $repairNok = Repair::with('igi')
            ->where('status', 'NOK')
            ->get()
            ->map(function($item) {
                return [
                    'igi_id' => $item->igi_id,
                    'serial_number' => $item->igi->serial_number,
                    'nama_barang' => $item->igi->nama_barang,
                    'type' => $item->igi->type,
                    'status' => $item->status,
                    'sumber' => 'REPAIR',
                    'timestamp' => $item->waktu_repair
                ];
            });

        $nokData = $ujiFungsiNok->concat($repairNok);

        // Apply search filter
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);
            $nokData = $nokData->filter(function($item) use ($search) {
                return stripos($item['serial_number'], $search) !== false ||
                       stripos($item['nama_barang'], $search) !== false ||
                       stripos($item['type'], $search) !== false;
            });
        }

        // Apply category filter
        if ($request->has('category') && $request->category != '' && $request->category != 'Semua') {
            $nokData = $nokData->filter(function($item) use ($request) {
                return $item['nama_barang'] === $request->category;
            });
        }

        return response()->json($nokData->sortByDesc('timestamp')->values());
    }

    public function checkSerial(Request $request)
    {
        $serialNumber = $request->serial_number;
        
        $igi = Igi::where('serial_number', $serialNumber)->first();
        
        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan dalam database IGI'
            ], 404);
        }

        // VALIDASI: Harus ada uji_fungsi NOK ATAU repair NOK
        $ujiFungsiNok = $igi->ujiFungsi()->where('status', 'NOK')->first();
        $repairNok = $igi->repair()->where('status', 'NOK')->first();

        if (!$ujiFungsiNok && !$repairNok) {
            return response()->json([
                'success' => false,
                'message' => 'Service Handling hanya untuk barang dengan status NOK dari Uji Fungsi atau Repair'
            ], 400);
        }

        $sumber = $repairNok ? 'REPAIR' : 'UJI_FUNGSI';

        return response()->json([
            'success' => true,
            'data' => [
                'igi_id' => $igi->id,
                'nama_barang' => $igi->nama_barang,
                'type' => $igi->type,
                'serial_number' => $igi->serial_number,
                'sumber' => $sumber
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'igi_id' => 'required|exists:igi,id',
            'sumber' => 'required|in:UJI_FUNGSI,REPAIR',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $igi = Igi::findOrFail($request->igi_id);
            
            // VALIDASI: Harus ada uji_fungsi NOK ATAU repair NOK
            $ujiFungsiNok = $igi->ujiFungsi()->where('status', 'NOK')->exists();
            $repairNok = $igi->repair()->where('status', 'NOK')->exists();

            if (!$ujiFungsiNok && !$repairNok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service Handling hanya untuk barang NOK'
                ], 400);
            }

            // Insert Service Handling
            $serviceHandling = ServiceHandling::create([
                'igi_id' => $igi->id,
                'sumber' => $request->sumber,
                'status' => 'NOK',
                'keterangan' => $request->keterangan,
                'waktu_service' => Carbon::now()
            ]);

            // Update status di IGI Operasional
            $igi->update(['status_proses' => 'SERVICE_HANDLING']);

            // Update status di IGI Master
            $igi->master->update(['status_proses' => 'SERVICE_HANDLING']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Service Handling berhasil disimpan',
                'data' => $serviceHandling->load('igi')
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $serviceHandling = ServiceHandling::with('igi.master')->findOrFail($id);
            $igi = $serviceHandling->igi;
            
            // ROLLBACK LOGIC: Kembali sesuai sumber
            if ($serviceHandling->sumber === 'REPAIR') {
                $igi->update(['status_proses' => 'REPAIR']);
                $igi->master->update(['status_proses' => 'REPAIR']);
            } else {
                $igi->update(['status_proses' => 'UJI_FUNGSI']);
                $igi->master->update(['status_proses' => 'UJI_FUNGSI']);
            }
            
            // Delete record service handling
            $serviceHandling->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data service handling berhasil dihapus. Status barang dikembalikan.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}