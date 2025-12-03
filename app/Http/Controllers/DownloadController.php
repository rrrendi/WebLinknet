<?php

namespace App\Http\Controllers;

use App\Models\Igi;
use App\Models\IgiMaster;
use App\Models\UjiFungsi;
use App\Models\Repair;
use App\Models\Rekondisi;
use App\Models\ServiceHandling;
use App\Models\Packing;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataExport;

class DownloadController extends Controller
{
    public function index()
    {
        // Get unique values for filters dari IGI operasional
        $namaBarangList = Igi::select('nama_barang')->distinct()->pluck('nama_barang');
        $typeList = Igi::select('type')->distinct()->pluck('type');
        
        return view('download.index', compact('namaBarangList', 'typeList'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'modul' => 'required',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        try {
            $modul = $request->modul;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $namaBarang = $request->nama_barang;
            $type = $request->type;
            $status = $request->status;

            // Build query based on modul
            $query = $this->getQueryByModul($modul);

            // Apply date filters
            if ($startDate) {
                $query->whereDate($this->getDateColumn($modul), '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate($this->getDateColumn($modul), '<=', $endDate);
            }
            
            // Apply nama_barang filter - FIX: Handle array dan "semua"
            if ($namaBarang && is_array($namaBarang) && count($namaBarang) > 0) {
                // Filter hanya jika ada pilihan selain empty string
                $filtered = array_filter($namaBarang, function($item) {
                    return $item !== '' && $item !== null;
                });
                
                if (count($filtered) > 0) {
                    if ($modul === 'igi' || $modul === 'igi_master') {
                        $query->whereIn('nama_barang', $filtered);
                    } else {
                        // Untuk tabel lain, join dengan igi
                        $query->whereHas('igi', function($q) use ($filtered) {
                            $q->whereIn('nama_barang', $filtered);
                        });
                    }
                }
            }
            
            // Apply type filter - FIX: Handle array dan "semua"
            if ($type && is_array($type) && count($type) > 0) {
                $filtered = array_filter($type, function($item) {
                    return $item !== '' && $item !== null;
                });
                
                if (count($filtered) > 0) {
                    if ($modul === 'igi' || $modul === 'igi_master') {
                        $query->whereIn('type', $filtered);
                    } else {
                        $query->whereHas('igi', function($q) use ($filtered) {
                            $q->whereIn('type', $filtered);
                        });
                    }
                }
            }
            
            // Apply status filter (untuk uji_fungsi dan repair)
            if ($status && in_array($modul, ['uji_fungsi', 'repair'])) {
                $query->where('status', $status);
            }

            // Get data
            $data = $query->get();
            
            // Check if data is empty
            if ($data->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada data yang sesuai dengan filter yang dipilih.');
            }

            $fileName = $this->getFileName($modul);

            return Excel::download(new DataExport($data, $modul), $fileName);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getQueryByModul($modul)
    {
        switch ($modul) {
            case 'igi':
                return Igi::query();
            case 'igi_master':
                return IgiMaster::query();
            case 'uji_fungsi':
                return UjiFungsi::with('igi')->select('uji_fungsi.*');
            case 'repair':
                return Repair::with('igi')->select('repair.*');
            case 'rekondisi':
                return Rekondisi::with('igi')->select('rekondisi.*');
            case 'service_handling':
                return ServiceHandling::with('igi')->select('service_handling.*');
            case 'packing':
                return Packing::with('igi')->select('packing.*');
            case 'semua':
                return IgiMaster::query(); // Export semua dari master
            default:
                return Igi::query();
        }
    }

    private function getDateColumn($modul)
    {
        $columns = [
            'igi' => 'tanggal_datang',
            'igi_master' => 'tanggal_datang',
            'uji_fungsi' => 'waktu_uji',
            'repair' => 'waktu_repair',
            'rekondisi' => 'waktu_rekondisi',
            'service_handling' => 'waktu_service',
            'packing' => 'waktu_packing'
        ];

        return $columns[$modul] ?? 'created_at';
    }

    private function getFileName($modul)
    {
        $date = date('d-m-Y');
        $names = [
            'igi' => "IGI_Operasional_{$date}.xlsx",
            'igi_master' => "IGI_Master_{$date}.xlsx",
            'uji_fungsi' => "Uji_Fungsi_{$date}.xlsx",
            'repair' => "Repair_{$date}.xlsx",
            'rekondisi' => "Rekondisi_{$date}.xlsx",
            'service_handling' => "Service_Handling_{$date}.xlsx",
            'packing' => "Packing_{$date}.xlsx",
            'semua' => "Semua_Data_{$date}.xlsx"
        ];

        return $names[$modul] ?? "Data_{$date}.xlsx";
    }
}