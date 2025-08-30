<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionTData1;
use App\Models\ProductionTData2;
use App\Models\ProductionTData3;
use App\Models\ProductionTData4;
use App\Models\Kode;                  // Import model Kode
use App\Models\SapUser;                 // Import model SapUser

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request, $kode)
    {
        // 1a) Buat query untuk mendapatkan daftar induk semua ARBPL unik untuk plant ini
        $allWcQuery = DB::table('production_t_data1')
            ->select('ARBPL')
            ->distinct()
            ->where('WERKSX', $kode);

        // 1b) Query utama: Mulai dari daftar induk, lalu LEFT JOIN ke data transaksi
        $statsPerWc = DB::table(DB::raw("({$allWcQuery->toSql()}) as master_wc"))
            ->mergeBindings($allWcQuery) // Penting untuk binding parameter
            ->leftJoin('production_t_data1 as trans_data', function ($join) use ($kode) {
                $join->on('master_wc.ARBPL', '=', 'trans_data.ARBPL')
                    ->where('trans_data.WERKSX', '=', $kode) // Pastikan join juga difilter berdasarkan plant
                    ->whereRaw("NULLIF(TRIM(trans_data.AUFNR), '') IS NOT NULL");
            })
            ->selectRaw("
                CASE
                    WHEN NULLIF(TRIM(master_wc.ARBPL), '') IS NULL
                    THEN 'Eksternal Workcenter'
                    ELSE master_wc.ARBPL
                END AS ARBPL_LABEL,
                COUNT(DISTINCT trans_data.AUFNR) AS pro_count,
                COALESCE(SUM(trans_data.CPCTYX), 0) AS total_capacity
            ")
            ->groupBy('ARBPL_LABEL')
            ->orderBy('ARBPL_LABEL', 'asc')
            ->get();

        // 2) Siapkan labels dan data untuk kedua dataset (Tidak ada perubahan di sini)
        $labels            = $statsPerWc->pluck('ARBPL_LABEL')->values()->all();
        $datasetPro        = $statsPerWc->pluck('pro_count')->map(fn($v) => (int)$v)->values()->all();
        $datasetCapacity   = $statsPerWc->pluck('total_capacity')->map(fn($v) => (float)$v)->values()->all();

        // 3) Definisikan kedua dataset untuk chart (Tidak ada perubahan di sini)
        $datasets = [
            [
                'label'           => 'Jumlah PRO',
                'data'            => $datasetPro,
                'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                'borderColor'     => 'rgba(37, 99, 235, 1)',
                'borderWidth'     => 1,
                'borderRadius'    => 4,
            ],
            [
                'label'           => 'Jumlah Kapasitas',
                'data'            => $datasetCapacity,
                'backgroundColor' => 'rgba(249, 115, 22, 0.6)',
                'borderColor'     => 'rgba(234, 88, 12, 1)',
                'borderWidth'     => 1,
                'borderRadius'    => 4,
            ],
        ];
        
        // =================================================================
        // DATA UNTUK CHART KEDUA (DOUGHNUT CHART - PER PLANT) - BARU
        // =================================================================
        $statsRelPerPlant = DB::table('production_t_data1')
            ->where('STATS', 'REL')
            ->whereRaw("NULLIF(TRIM(AUFNR), '') IS NOT NULL")
            ->select('WERKSX', DB::raw('COUNT(DISTINCT AUFNR) as pro_count_rel'))
            ->groupBy('WERKSX')
            ->orderBy('WERKSX', 'asc')
            ->get();

        $doughnutChartLabels = $statsRelPerPlant->pluck('WERKSX')->values()->all();
        $doughnutChartDataset = $statsRelPerPlant->pluck('pro_count_rel')->values()->all();

        // Dataset untuk doughnut/pie chart memiliki struktur yang sedikit berbeda
        $doughnutChartDatasets = [
            [
                'label' => 'Jumlah PRO Status REL',
                'data'  => $doughnutChartDataset,
                'backgroundColor' => [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                'borderColor' => '#ffffff',
                'borderWidth' => 2,
            ]
        ];
        // Sisa kode Anda tidak berubah
        $TData1 = ProductionTData1::count();
        $TData2 = ProductionTData2::count();
        $TData3 = ProductionTData3::count();
        $TData4 = ProductionTData4::count();

        $outstandingReservasi = ProductionTData4::whereColumn('KALAB', '<', 'BDMNG')->count();

        return view('Admin.dashboard', compact('TData1', 'TData2', 'TData3', 'outstandingReservasi', 'labels', 'datasets','doughnutChartLabels','doughnutChartDatasets'));
    }

    public function AdminDashboard()
    {
        // Inisialisasi koleksi kosong untuk menampung data plant
        $plants = collect();

        // Pastikan pengguna sudah login sebelum mengambil data
        if (Auth::check()) {
            $user = Auth::user();
            $sapUser = null;

            // Logika untuk menemukan SapUser berdasarkan peran (role) pengguna
            if ($user->role === 'admin') {
                // 1. Ambil 'sap_id' dari email pengguna
                $sapId = str_replace('@kmi.local', '', $user->email);
                // 2. Cari SapUser berdasarkan sap_id
                $sapUser = SapUser::where('sap_id', $sapId)->first();
            } 
            elseif ($user->role === 'korlap') {
                // 1. Ambil 'nik' dari email pengguna
                $nik = str_replace('@kmi.local', '', $user->email);
                // 2. Cari record di tabel 'kodes' berdasarkan NIK
                $kode = Kode::where('nik', $nik)->first();
                
                // 3. Jika ditemukan, ambil SapUser yang berelasi
                if ($kode) {
                    $sapUser = $kode->sapUser;
                }
            }

            // Jika SapUser ditemukan, ambil semua 'kodes' (plant) yang berelasi dengannya
            if ($sapUser) {
                $plants = $sapUser->kode()->get();
            }
        }
        
        // Kirim data 'plants' ke view 'dashboard-landing'
        return view('dashboard', compact('plants'));
    }
}