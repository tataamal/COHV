<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionTData1;
use App\Models\ProductionTData2;
use App\Models\ProductionTData3;
use App\Models\ProductionTData4;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request, $kode)
    {
        // 1) Hitung jumlah PRO per ARBPL, ARBPL kosong -> "Eksternal Workcenter"
        $proPerWc = DB::table('production_t_data1')
            ->when($kode, fn($q) => $q->where('WERKSX', $kode)) // ganti kolom filter sesuai skema Anda
            // hanya hitung baris yang memang ada AUFNR-nya
            ->whereRaw("NULLIF(TRIM(AUFNR), '') IS NOT NULL")
            ->selectRaw("
                CASE
                    WHEN NULLIF(TRIM(ARBPL), '') IS NULL
                    THEN 'Eksternal Workcenter'
                    ELSE ARBPL
                END AS ARBPL_LABEL,
                COUNT(*) AS pro_count
                -- Jika 1 PRO bisa muncul >1 baris, pakai ini:
                -- COUNT(DISTINCT AUFNR) AS pro_count
            ")
            ->groupBy('ARBPL_LABEL')
            ->orderBy('ARBPL_LABEL', 'asc')
            ->get();

        // 2) Labels & data untuk chart
        $labels   = $proPerWc->pluck('ARBPL_LABEL')->values()->all();
        $datasetPro = $proPerWc->pluck('pro_count')->map(fn($v) => (int)$v)->values()->all();

        // 3) Dataset chart
        $datasets = [
            [
                'label' => 'Jumlah PRO per Workcenter',
                'data'  => $datasetPro,
                'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                'borderColor'     => 'rgba(37, 99, 235, 1)',
                'borderWidth'     => 1,
                'borderRadius'    => 4,
            ],
        ];
        
        $TData1 = ProductionTData1::count();
        $TData2 = ProductionTData2::count();
        $TData3 = ProductionTData3::count();
        $TData4 = ProductionTData4::count();

        $outstandingReservasi = ProductionTData4::whereColumn('KALAB', '<', 'BDMNG')->count();
    
        return view('Admin.dashboard', compact('TData1', 'TData2', 'TData3', 'outstandingReservasi', 'labels','datasets'));
    }

    public function AdminDashboard()
    {
        $TData1 = ProductionTData1::count();
        $TData2 = ProductionTData2::count();
        $TData3 = ProductionTData3::count();
        $TData4 = ProductionTData4::count();

        $outstandingReservasi = ProductionTData4::whereColumn('KALAB', '<', 'BDMNG')->count();
        return view('dashboard', compact('TData1', 'TData2', 'TData3', 'outstandingReservasi'));
    }


    // /**
    //  * Show orders list page.
    //  */
    // public function orders()
    // {
    //     // Add logic for orders listing
    //     return view('admin.orders.index');
    // }

    // /**
    //  * Show users management page.
    //  */
    // public function users()
    // {
    //     // Add logic for user management
    //     return view('admin.users.index');
    // }

    // /**
    //  * Show reports page.
    //  */
    // public function reports()
    // {
    //     // Add logic for reports
    //     return view('');
    // }
}