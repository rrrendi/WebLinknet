<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\{IgiBapb, IgiDetail, UjiFungsi, Repair, Rekondisi, ServiceHandling, Packing};

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bapb' => IgiBapb::count(),
            'total_barang' => IgiDetail::count(),
            'total_uji_ok' => UjiFungsi::where('result', 'OK')->count(),
            'total_uji_nok' => UjiFungsi::where('result', 'NOK')->count(),
            'total_repair_ok' => Repair::where('result', 'OK')->count(),
            'total_repair_nok' => Repair::where('result', 'NOK')->count(),
            'total_rekondisi' => Rekondisi::count(),
            'total_service' => ServiceHandling::count(),
            'total_packing' => Packing::count(),
        ];

        // Per Status
        $statusBreakdown = [
            'IGI' => IgiDetail::where('status_proses', 'IGI')->count(),
            'UJI_FUNGSI' => IgiDetail::where('status_proses', 'UJI_FUNGSI')->count(),
            'REPAIR' => IgiDetail::where('status_proses', 'REPAIR')->count(),
            'REKONDISI' => IgiDetail::where('status_proses', 'REKONDISI')->count(),
            'SERVICE_HANDLING' => IgiDetail::where('status_proses', 'SERVICE_HANDLING')->count(),
            'PACKING' => IgiDetail::where('status_proses', 'PACKING')->count(),
        ];

        // Per Jenis
        $jenisList = ['STB', 'ONT', 'ROUTER'];
        $chartData = [];
        foreach ($jenisList as $jenis) {
            $chartData[$jenis] = [
                'total' => IgiDetail::where('jenis', $jenis)->count(),
                'packing' => IgiDetail::where('jenis', $jenis)->where('status_proses', 'PACKING')->count(),
            ];
        }

        return view('dashboard', compact('stats', 'statusBreakdown', 'chartData'));
    }
}