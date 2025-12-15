<?php

namespace App\Http\Controllers;

use App\Models\{IgiBapb, IgiDetail, UjiFungsi, Repair, Rekondisi, ServiceHandling, Packing};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataExport;

class DownloadController extends Controller
{
    public function index()
    {
        $pemilikList = ['Linknet', 'Telkomsel'];
        $wilayahList = IgiBapb::select('wilayah')->distinct()->pluck('wilayah');
        
        return view('download.index', compact('pemilikList', 'wilayahList'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'modul' => 'required|in:igi,uji_fungsi,repair,rekondisi,service_handling,packing',
            'pemilik' => 'nullable|in:Linknet,Telkomsel',
            'wilayah' => 'nullable|string',
            'tanggal_datang' => 'nullable|date',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal'
        ]);

        try {
            $query = $this->getQueryByModul($request->modul);

            // Filter by BAPB
            if ($request->filled('pemilik') || $request->filled('wilayah') || $request->filled('tanggal_datang')) {
                $query->whereHas('igiDetail.bapb', function($q) use ($request) {
                    if ($request->filled('pemilik')) {
                        $q->where('pemilik', $request->pemilik);
                    }
                    if ($request->filled('wilayah')) {
                        $q->where('wilayah', $request->wilayah);
                    }
                    if ($request->filled('tanggal_datang')) {
                        $q->whereDate('tanggal_datang', $request->tanggal_datang);
                    }
                });
            }

            // Filter by date range
            if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $dateColumn = $this->getDateColumn($request->modul);
                $query->whereBetween($dateColumn, [$request->tanggal_awal, $request->tanggal_akhir]);
            }

            $data = $query->with(['igiDetail.bapb', 'user'])->get();

            if ($data->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data yang sesuai filter!');
            }

            $fileName = $this->getFileName($request->modul);
            return Excel::download(new DataExport($data, $request->modul), $fileName);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function getQueryByModul($modul)
    {
        return match($modul) {
            'igi' => IgiDetail::query(),
            'uji_fungsi' => UjiFungsi::query(),
            'repair' => Repair::query(),
            'rekondisi' => Rekondisi::query(),
            'service_handling' => ServiceHandling::query(),
            'packing' => Packing::query(),
        };
    }

    private function getDateColumn($modul)
    {
        return match($modul) {
            'igi' => 'scan_time',
            'uji_fungsi' => 'uji_fungsi_time',
            'repair' => 'repair_time',
            'rekondisi' => 'rekondisi_time',
            'service_handling' => 'service_time',
            'packing' => 'packing_time',
        };
    }

    private function getFileName($modul)
    {
        $date = date('d-m-Y');
        return ucfirst(str_replace('_', ' ', $modul)) . "_{$date}.xlsx";
    }
}