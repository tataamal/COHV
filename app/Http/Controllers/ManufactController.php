<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ProductionTData1;
use App\Models\ProductionTData2;
use App\Models\ProductionTData3;
use App\Models\ProductionTData4;
use Carbon\Carbon;



class ManufactController extends Controller
{
    public function DetailData2(string $kode)
    {
        set_time_limit(0);

        try {
            $response = Http::timeout(0)->withHeaders([
                'X-SAP-Username' => session('username'),
                'X-SAP-Password' => session('password'),
            ])->get('http://127.0.0.1:8006/api/sap_combined', [
                'plant' => $kode
            ]);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari SAP.');
            }

            $data = $response->json();
            
            // dd('Data berhasil diambil', $data);
        function formatTanggal($tgl)
        {
            if (empty($tgl)) return null;

            try {
                return Carbon::createFromFormat('Ymd', $tgl)->format('d-m-Y');
            } catch (\Exception $e) {
                return $tgl; // fallback, kalau format tidak sesuai
            }
        }
        
            // == T_DATA1
            $orderx1 = [];

            foreach ($data['T_DATA1'] ?? [] as $row) {
                // dd($row);
                $orderx = $row['ORDERX'] ?? null;
                $vornr = $row['VORNR'] ?? null;
                if ($orderx) $orderx1[] = [$orderx, $vornr];

                // Buat field PV1, PV2, PV3 jika datanya ada
                $sssl1 = formatTanggal($row['SSSLDPV1'] ?? '');
                $sssl2 = formatTanggal($row['SSSLDPV2'] ?? '');
                $sssl3 = formatTanggal($row['SSSLDPV3'] ?? '');

                $pv1 = (!empty($row['ARBPL1']) && !empty($sssl1)) ? strtoupper($row['ARBPL1'] . ' - ' . $sssl1) : null;
                $pv2 = (!empty($row['ARBPL2']) && !empty($sssl2)) ? strtoupper($row['ARBPL2'] . ' - ' . $sssl2) : null;
                $pv3 = (!empty($row['ARBPL3']) && !empty($sssl3)) ? strtoupper($row['ARBPL3'] . ' - ' . $sssl3) : null;

                // dd($orderx, $vornr, $pv1, $pv2, $pv3);

                ProductionTData1::updateOrCreate([
                    'ORDERX' => $orderx,
                    'VORNR' => $vornr,
                    ], array_merge($row, [
                        'PV1' => $pv1,
                        'PV2' => $pv2,
                        'PV3' => $pv3,
                    ]));

                // dd($e);
                
            }


            // == T_DATA2
            $kd2WithKey = [];
            $kd2WithoutKey = [];

            foreach ($data['T_DATA2'] ?? [] as $row) {
                $row['WERKSX'] = $kode;

                if (empty($row['EDATU'])) $row['EDATU'] = null;

                try {
                    if (!empty($row['KDAUF']) && !empty($row['KDPOS'])) {
                        $kd2WithKey[] = [$row['KDAUF'], $row['KDPOS']];
                        ProductionTData2::updateOrCreate([
                            'KDAUF' => $row['KDAUF'],
                            'KDPOS' => $row['KDPOS'],
                        ], $row);
                    } else {
                        // tetap simpan dan lacak dengan ID setelah create
                        $created = ProductionTData2::create($row);
                        $kd2WithoutKey[] = $created->id;
                    }
                } catch (\Exception $e) {
                    Log::warning('Gagal simpan T_DATA2', ['row' => $row, 'error' => $e->getMessage()]);
                }
            }


            // == T_DATA3
            $orderx3 = [];

            foreach ($data['T_DATA3'] ?? [] as $row) {
                if (!isset($row['ORDERX'])) continue;

                $orderx3[] = [$row['ORDERX'], $row['VORNR'] ?? null];

                ProductionTData3::updateOrCreate([
                    'ORDERX' => $row['ORDERX'],
                    'VORNR' => $row['VORNR'] ?? null,
                ], $row);
            }

            // == T_DATA4
            $rsnum4 = [];

            foreach ($data['T_DATA4'] ?? [] as $row) {
                if (!isset($row['RSNUM']) || !isset($row['RSPOS'])) continue;

                $rsnum4[] = [$row['RSNUM'], $row['RSPOS']];

                ProductionTData4::updateOrCreate([
                    'RSNUM' => $row['RSNUM'],
                    'RSPOS' => $row['RSPOS'],
                ], $row);
            }

            // === DELETE DATA YANG TIDAK ADA DI SAP ===
            // === DELETE T_DATA1 YANG TIDAK ADA ===
            $existing1 = ProductionTData1::all();
            $toKeep1 = collect($orderx1);
            foreach ($existing1 as $item) {
                if (!$toKeep1->contains(function ($val) use ($item) {
                    return $val[0] === $item->ORDERX && $val[1] === $item->VORNR;
                })) {
                    $item->delete();
                }
            }

            // === DELETE T_DATA2 YANG TIDAK ADA (khusus plant itu saja) ===
            $existing2 = ProductionTData2::where('WERKSX', $kode)->get();
            $toKeep2WithKey = collect($kd2WithKey);
            $toKeep2WithoutKey = collect($kd2WithoutKey);

            foreach ($existing2 as $item) {
                if (!empty($item->KDAUF) && !empty($item->KDPOS)) {
                    if (!$toKeep2WithKey->contains(function ($val) use ($item) {
                        return $val[0] === $item->KDAUF && $val[1] === $item->KDPOS;
                    })) {
                        $item->delete();
                    }
                } else {
                    if (!$toKeep2WithoutKey->contains($item->id)) {
                        $item->delete();
                    }
                }
            }


            // === DELETE T_DATA3 YANG TIDAK ADA ===
            $existing3 = ProductionTData3::all();
            $toKeep3 = collect($orderx3);
            foreach ($existing3 as $item) {
                if (!$toKeep3->contains(function ($val) use ($item) {
                    return $val[0] === $item->ORDERX && $val[1] === $item->VORNR;
                })) {
                    $item->delete();
                }
            }

            // === DELETE T_DATA4 YANG TIDAK ADA ===
            $existing4 = ProductionTData4::all();
            $toKeep4 = collect($rsnum4);
            foreach ($existing4 as $item) {
                if (!$toKeep4->contains(function ($val) use ($item) {
                    return $val[0] === $item->RSNUM && $val[1] === $item->RSPOS;
                })) {
                    $item->delete();
                }
            }

            return redirect()->route('show.detail.data2', $kode)
                ->with('success', 'Data berhasil disinkronkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    public function showDetail(Request $request, $kode)
    {
        $kodeInfo = Kode::where('kode', $kode)->firstOrFail();
        $query = ProductionTData2::where('WERKSX', $kode);

        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('KDAUF', 'like', "%{$search}%")
                  ->orWhere('MATFG', 'like', "%{$search}%")
                  ->orWhere('MAKFG', 'like', "%{$search}%");
            });
        }

        // Gunakan paginasi untuk data yang banyak
        $details = $query->paginate(10);

        // Ambil KDAUF dari hasil paginasi untuk query data terkait
        $kdaufValues = $details->pluck('KDAUF')->unique()->filter();

        // Ambil semua TData3 yang relevan sekaligus
        $allTData3 = ProductionTData3::whereIn('KDAUF', $kdaufValues)
            ->get();

        // Ambil kunci untuk query TData1 dan TData4
        $orderxVornrKeys = $allTData3->map(fn($item) => $item->ORDERX . '-' . $item->VORNR)->unique();
        $aufnrValues = $allTData3->pluck('AUFNR')->unique()->filter();
        $plnumValues = $allTData3->pluck('PLNUM')->unique()->filter();

        // Ambil semua TData1 yang relevan
        $allTData1 = ProductionTData1::whereIn(DB::raw("CONCAT(ORDERX, '-', VORNR)"), $orderxVornrKeys)
            ->get()
            ->groupBy(fn($item) => $item->ORDERX . '-' . $item->VORNR);

        // Ambil semua TData4 yang relevan
        $allTData4ByAufnr = ProductionTData4::whereIn('AUFNR', $aufnrValues)
            ->get()
            ->groupBy('AUFNR');
        
        $allTData4ByPlnum = ProductionTData4::whereIn('PLNUM', $plnumValues)
            ->get()
            ->groupBy('PLNUM');

        // Group TData3 untuk kemudahan di view
        $allTData3Grouped = $allTData3->groupBy(fn($item) => $item->KDAUF . '-' . $item->KDPOS);

        return view('Admin.detail-data2', [
            'plant' => $kode,
            'categories' => $kodeInfo->kategori,
            'bagian' => $kodeInfo->nama_bagian,
            'details' => $details,
            'allTData3' => $allTData3Grouped,
            'allTData1' => $allTData1,
            'allTData4ByAufnr' => $allTData4ByAufnr,
            'allTData4ByPlnum' => $allTData4ByPlnum,
            'search' => $search,
        ]);
    }
}
