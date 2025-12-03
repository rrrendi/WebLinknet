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

class DashboardController extends Controller
{
    public function index()
    {
        // Total IGI (Operasional - yang sedang aktif)
        $totalIgi = Igi::count();
        
        // Total IGI Master (semua histori termasuk yang sudah packing)
        $totalIgiMaster = IgiMaster::count();
        
        // Total Uji Fungsi
        $totalUjiFungsiOk = UjiFungsi::where('status', 'OK')->count();
        $totalUjiFungsiNok = UjiFungsi::where('status', 'NOK')->count();
        $totalUjiFungsi = $totalUjiFungsiOk + $totalUjiFungsiNok;
        
        // Total Repair
        $totalRepairOk = Repair::where('status', 'OK')->count();
        $totalRepairNok = Repair::where('status', 'NOK')->count();
        $totalRepair = $totalRepairOk + $totalRepairNok;
        
        // Total Rekondisi
        $totalRekondisi = Rekondisi::count();
        
        // Total Service Handling
        $totalServiceHandling = ServiceHandling::count();
        
        // Total Packing (dari IGI Master dengan status PACKING)
        $totalPacking = IgiMaster::where('status_proses', 'PACKING')->count();
        
        // Total Stok Keseluruhan (dari Master)
        $totalStok = $totalIgiMaster;
        
        // Breakdown by status proses di IGI Operasional
        $statusBreakdown = [
            'IGI' => Igi::where('status_proses', 'IGI')->count(),
            'UJI_FUNGSI' => Igi::where('status_proses', 'UJI_FUNGSI')->count(),
            'REPAIR' => Igi::where('status_proses', 'REPAIR')->count(),
            'REKONDISI' => Igi::where('status_proses', 'REKONDISI')->count(),
            'SERVICE_HANDLING' => Igi::where('status_proses', 'SERVICE_HANDLING')->count(),
            'PACKING' => Igi::where('status_proses', 'PACKING')->count(),
        ];
        
        // Chart data per kategori
        $chartData = [
            'ONT' => [
                'total' => IgiMaster::where('nama_barang', 'ONT')->count(),
                'packing' => IgiMaster::where('nama_barang', 'ONT')->where('status_proses', 'PACKING')->count(),
            ],
            'STB' => [
                'total' => IgiMaster::where('nama_barang', 'STB')->count(),
                'packing' => IgiMaster::where('nama_barang', 'STB')->where('status_proses', 'PACKING')->count(),
            ],
            'ROUTER' => [
                'total' => IgiMaster::where('nama_barang', 'ROUTER')->count(),
                'packing' => IgiMaster::where('nama_barang', 'ROUTER')->where('status_proses', 'PACKING')->count(),
            ],
        ];
        
        return view('dashboard', compact(
            'totalIgi',
            'totalIgiMaster',
            'totalUjiFungsiOk',
            'totalUjiFungsiNok',
            'totalUjiFungsi',
            'totalRepairOk',
            'totalRepairNok',
            'totalRepair',
            'totalRekondisi',
            'totalServiceHandling',
            'totalPacking',
            'totalStok',
            'statusBreakdown',
            'chartData'
        ));
    }
}