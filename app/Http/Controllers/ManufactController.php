<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\ProductionTData;
use App\Models\ProductionTData1;
use App\Models\ProductionTData2;
use App\Models\ProductionTData3;
use App\Models\ProductionTData4;
use Carbon\Carbon;
use Illuminate\Support\Arr;



class ManufactController extends Controller
{
    // public function refresh(string $kode, Request $request) {
    //     set_time_limit(0);
    //     // AUFNR bisa dikirim sebagai array atau string "a,b,c"
    //     $orders = $request->input('AUFNR');
    //     if (is_string($orders)) {
    //         $orders = array_filter(array_map('trim', explode(',', $orders)));
    //     } elseif (!is_array($orders)) {
    //         $orders = [];
    //     }

    //     // helper tanggal
    //     $formatTanggal = function ($tgl) {
    //         if (empty($tgl)) return null;
    //         try { return Carbon::createFromFormat('Ymd', $tgl)->format('d-m-Y'); }
    //         catch (\Exception $e) { return $tgl; }
    //     };

    //     try {
    //         $http = Http::timeout(0)
    //             ->acceptJson()
    //             ->withHeaders([
    //                 'X-SAP-Username' => session('username'),
    //                 'X-SAP-Password' => session('password'),
    //             ]);

    //         // 1) Ambil data dari Flask
    //         if (!empty($orders)) {
    //             // batch/single AUFNR → POST /api/data_refresh
    //             $resp = $http->post('http://127.0.0.1:8006/api/data_refresh', [
    //                 'plant' => $kode,
    //                 'AUFNR' => $orders, // array
    //             ]);
    //         } else {
    //             // tanpa AUFNR → fallback lama (plant saja)
    //             $resp = $http->get('http://127.0.0.1:8006/api/sap_combined', [
    //                 'plant' => $kode
    //             ]);
    //         }

    //         if (!$resp->successful()) {
    //             return back()->with('error', 'Gagal mengambil data dari SAP.');
    //         }

    //         $payload = $resp->json();

    //         // 2) Normalisasi payload → $T_DATA..$T4 (gabung jika results[])
    //         $T_DATA = $T1 = $T2 = $T3 = $T4 = [];
    //         if (isset($payload['results']) && is_array($payload['results'])) {
    //             foreach ($payload['results'] as $res) {
    //                 foreach (($res['T_DATA']) ?? [] as $r) $T_DATA[] = $r;
    //                 foreach (($res['T_DATA1'] ?? []) as $r) $T1[] = $r;
    //                 foreach (($res['T_DATA2'] ?? []) as $r) $T2[] = $r;
    //                 foreach (($res['T_DATA3'] ?? []) as $r) $T3[] = $r;
    //                 foreach (($res['T_DATA4'] ?? []) as $r) $T4[] = $r;
    //             }
    //         } else {
    //             $T_DATA = $payload['T_DATA']  ?? [];
    //             $T1     = $payload['T_DATA1'] ?? [];
    //             $T2     = $payload['T_DATA2'] ?? [];
    //             $T3     = $payload['T_DATA3'] ?? [];
    //             $T4     = $payload['T_DATA4'] ?? [];

    //             // jadikan selalu list of arrays
    //             if (!empty($T_DATA) && Arr::isAssoc($T_DATA)) $T_DATA = [$T_DATA];
    //             if (!empty($T1)     && Arr::isAssoc($T1))     $T1     = [$T1];
    //             if (!empty($T2)     && Arr::isAssoc($T2))     $T2     = [$T2];
    //             if (!empty($T3)     && Arr::isAssoc($T3))     $T3     = [$T3];
    //             if (!empty($T4)     && Arr::isAssoc($T4))     $T4     = [$T4];
    //         }

    //         // 3) Proses & simpan — gunakan transaksi biar aman
    //         DB::beginTransaction();

    //         $keep0WithKey = []; // pasangan [KDAUF, KDPOS] yang diterima
    //         foreach ($T_DATA as $row) {
    //             // tandai plant utk scope delete
    //             $row['WERKSX'] = $kode;

    //             // konsistensi dengan T_DATA2: EDATU dibiarkan string SAP (YYYYMMDD) atau null
    //             if (empty($row['EDATU'])) $row['EDATU'] = null;

    //             try {
    //                 if (!empty($row['KDAUF']) && !empty($row['KDPOS'])) {
    //                     // kalau ingin lebih ketat per-plant, jadikan kunci: ['KDAUF'=>..., 'KDPOS'=>..., 'WERKSX'=>$kode]
    //                     ProductionTData::updateOrCreate(
    //                         ['KDAUF' => $row['KDAUF'], 'KDPOS' => $row['KDPOS']],
    //                         $row
    //                     );
    //                     $keep0WithKey[] = [$row['KDAUF'], $row['KDPOS']];
    //                 } else {
    //                     // fallback bila key tak lengkap (jarang terjadi di T_DATA)
    //                     $created = ProductionTData::create($row);
    //                     // kalau perlu, simpan id utk delete—tapi idealnya T_DATA selalu punya KDAUF+KDPOS
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::warning('Gagal simpan T_DATA', ['row' => $row, 'error' => $e->getMessage()]);
    //             }
    //         }

    //         // ===== T_DATA1 =====
    //         $keep1 = []; // pasangan [ORDERX, VORNR] yang dikembalikan
    //         foreach ($T1 as $row) {
    //             $orderx = $row['ORDERX'] ?? null;
    //             $vornr  = $row['VORNR'] ?? null;
    //             if (!$orderx) continue;

    //             $sssl1 = $formatTanggal($row['SSSLDPV1'] ?? '');
    //             $sssl2 = $formatTanggal($row['SSSLDPV2'] ?? '');
    //             $sssl3 = $formatTanggal($row['SSSLDPV3'] ?? '');

    //             $pv1 = (!empty($row['ARBPL1']) && !empty($sssl1)) ? strtoupper($row['ARBPL1'].' - '.$sssl1) : null;
    //             $pv2 = (!empty($row['ARBPL2']) && !empty($sssl2)) ? strtoupper($row['ARBPL2'].' - '.$sssl2) : null;
    //             $pv3 = (!empty($row['ARBPL3']) && !empty($sssl3)) ? strtoupper($row['ARBPL3'].' - '.$sssl3) : null;

    //             // opsional: tandai plant untuk scoping delete
    //             $row['WERKSX'] = $kode;

    //             ProductionTData1::updateOrCreate(
    //                 ['ORDERX' => $orderx, 'VORNR' => $vornr],
    //                 array_merge($row, ['PV1'=>$pv1, 'PV2'=>$pv2, 'PV3'=>$pv3])
    //             );

    //             $keep1[] = [$orderx, $vornr];
    //         }

    //         // ===== T_DATA2 =====
    //         $keep2WithKey = [];
    //         $keep2Ids     = [];

    //         foreach ($T2 as $row) {
    //             $row['WERKSX'] = $kode; // kamu sudah pakai ini utk scope plant
    //             if (empty($row['EDATU'])) $row['EDATU'] = null;

    //             try {
    //                 if (!empty($row['KDAUF']) && !empty($row['KDPOS'])) {
    //                     ProductionTData2::updateOrCreate(
    //                         ['KDAUF'=>$row['KDAUF'], 'KDPOS'=>$row['KDPOS']],
    //                         $row
    //                     );
    //                     $keep2WithKey[] = [$row['KDAUF'], $row['KDPOS']];
    //                 } else {
    //                     $created = ProductionTData2::create($row);
    //                     $keep2Ids[] = $created->id;
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::warning('Gagal simpan T_DATA2', ['row'=>$row, 'error'=>$e->getMessage()]);
    //             }
    //         }

    //         // ===== T_DATA3 =====
    //         $keep3 = []; // pasangan [ORDERX, VORNR]
    //         foreach ($T3 as $row) {
    //             if (!isset($row['ORDERX'])) continue;
    //             // opsional: tandai plant
    //             $row['WERKSX'] = $kode;

    //             ProductionTData3::updateOrCreate(
    //                 ['ORDERX'=>$row['ORDERX'], 'VORNR'=>$row['VORNR'] ?? null],
    //                 $row
    //             );
    //             $keep3[] = [$row['ORDERX'], $row['VORNR'] ?? null];
    //         }

    //         // ===== T_DATA4 =====
    //         $keep4 = []; // pasangan [RSNUM, RSPOS]
    //         foreach ($T4 as $row) {
    //             if (!isset($row['RSNUM']) || !isset($row['RSPOS'])) continue;
    //             // opsional: tandai plant
    //             $row['WERKSX'] = $kode;

    //             ProductionTData4::updateOrCreate(
    //                 ['RSNUM'=>$row['RSNUM'], 'RSPOS'=>$row['RSPOS']],
    //                 $row
    //             );
    //             $keep4[] = [$row['RSNUM'], $row['RSPOS']];
    //         }

    //         // 4) DELETE yang tidak ada — dibatasi scope
    //         // Scope kita: kalau ada $orders → hapus hanya untuk ORDERX dalam $orders.
    //         // Kalau tidak ada $orders (refresh full plant): pertahankan logika lama tapi filter by plant bila ada kolomnya.

    //         $toKeep0WithKey = collect($keep0WithKey);

    //         if (!empty($orders)) {
    //             // Hanya sentuh SO (KDAUF) yang muncul di payload sekarang
    //             $kds = array_values(array_unique(array_map(fn($v) => $v[0], $keep0WithKey))); // list KDAUF
    //             if (!empty($kds)) {
    //                 $existing0 = ProductionTData::where('WERKSX', $kode)->whereIn('KDAUF', $kds)->get();
    //             } else {
    //                 $existing0 = collect(); // tidak ada apa-apa untuk dihapus
    //             }
    //         } else {
    //             // Full refresh per plant
    //             $existing0 = ProductionTData::where('WERKSX', $kode)->get();
    //         }

    //         foreach ($existing0 as $item) {
    //             if (!empty($item->KDAUF) && !empty($item->KDPOS)) {
    //                 if (!$toKeep0WithKey->contains(fn($v) => $v[0] === $item->KDAUF && $v[1] === $item->KDPOS)) {
    //                     $item->delete();
    //                 }
    //             }
    //         }

    //         // --- T_DATA1 ---
    //         if (!empty($orders)) {
    //             $existing1 = ProductionTData1::whereIn('ORDERX', $orders)->get();
    //         } else {
    //             // kalau ada kolom WERKSX pakai ini, kalau tidak ada → hati2: akan global
    //             $existing1 = ProductionTData1::where('WERKSX', $kode)->get();
    //         }
    //         $toKeep1 = collect($keep1);
    //         foreach ($existing1 as $item) {
    //             if (!$toKeep1->contains(fn($v) => $v[0]===$item->ORDERX && $v[1]===$item->VORNR)) {
    //                 $item->delete();
    //             }
    //         }

    //         // --- T_DATA2 (sudah kamu batasi per plant) ---
    //         $existing2 = ProductionTData2::where('WERKSX', $kode)->get();
    //         $toKeep2WithKey = collect($keep2WithKey);
    //         $toKeep2Ids     = collect($keep2Ids);
    //         foreach ($existing2 as $item) {
    //             if (!empty($item->KDAUF) && !empty($item->KDPOS)) {
    //                 if (!$toKeep2WithKey->contains(fn($v)=>$v[0]===$item->KDAUF && $v[1]===$item->KDPOS)) {
    //                     $item->delete();
    //                 }
    //             } else {
    //                 if (!$toKeep2Ids->contains($item->id)) {
    //                     $item->delete();
    //                 }
    //             }
    //         }

    //         // --- T_DATA3 ---
    //         if (!empty($orders)) {
    //             $existing3 = ProductionTData3::whereIn('ORDERX', $orders)->get();
    //         } else {
    //             $existing3 = ProductionTData3::where('WERKSX', $kode)->get();
    //         }
    //         $toKeep3 = collect($keep3);
    //         foreach ($existing3 as $item) {
    //             if (!$toKeep3->contains(fn($v)=>$v[0]===$item->ORDERX && $v[1]===$item->VORNR)) {
    //                 $item->delete();
    //             }
    //         }

    //         // --- T_DATA4 ---
    //         // T_DATA4 tidak punya ORDERX di kodenya—jadi kita batasi delete hanya pada RSNUM yang terkait response ini.
    //         // Ini menghindari penghapusan global.
    //         if (!empty($keep4)) {
    //             $existing4 = ProductionTData4::whereIn('RSNUM', array_unique(array_map(fn($v)=>$v[0], $keep4)))->get();
    //             $toKeep4 = collect($keep4);
    //             foreach ($existing4 as $item) {
    //                 if (!$toKeep4->contains(fn($v)=>$v[0]===$item->RSNUM && $v[1]===$item->RSPOS)) {
    //                     $item->delete();
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return redirect()->route('show.detail.data2', $kode)
    //             ->with('success', 'Data berhasil disinkronkan.');

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Gagal sinkronisasi: '.$e->getMessage());
    //     }
    // }
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

            $payload = $response->json();

            // helper tanggal (SAP Ymd -> d-m-Y) untuk tampilan jika diperlukan
            $formatTanggal = function ($tgl) {
                if (empty($tgl)) return null;
                try { return Carbon::createFromFormat('Ymd', $tgl)->format('d-m-Y'); }
                catch (\Exception $e) { return $tgl; }
            };

            // ========================
            // 1) Normalisasi payload
            // ========================
            $T_DATA = $T1 = $T2 = $T3 = $T4 = [];

            if (isset($payload['results']) && is_array($payload['results'])) {
                foreach ($payload['results'] as $res) {
                    foreach (($res['T_DATA']  ?? []) as $r) $T_DATA[] = $r;
                    foreach (($res['T_DATA1'] ?? []) as $r) $T1[]     = $r;
                    foreach (($res['T_DATA2'] ?? []) as $r) $T2[]     = $r;
                    foreach (($res['T_DATA3'] ?? []) as $r) $T3[]     = $r;
                    foreach (($res['T_DATA4'] ?? []) as $r) $T4[]     = $r;
                }
            } else {
                $T_DATA = $payload['T_DATA']  ?? [];
                $T1     = $payload['T_DATA1'] ?? [];
                $T2     = $payload['T_DATA2'] ?? [];
                $T3     = $payload['T_DATA3'] ?? [];
                $T4     = $payload['T_DATA4'] ?? [];

                // jika salah satu datang sebagai objek tunggal -> bungkus jadi array
                if (!empty($T_DATA) && Arr::isAssoc($T_DATA)) $T_DATA = [$T_DATA];
                if (!empty($T1)     && Arr::isAssoc($T1))     $T1     = [$T1];
                if (!empty($T2)     && Arr::isAssoc($T2))     $T2     = [$T2];
                if (!empty($T3)     && Arr::isAssoc($T3))     $T3     = [$T3];
                if (!empty($T4)     && Arr::isAssoc($T4))     $T4     = [$T4];
            }

            // ==================================
            // 2) Upsert & kumpulkan "keep" list
            // ==================================

            // == T_DATA (BARU) — key: (WERKSX, KUNNR, NAME1)
            $keep0 = []; // simpan kombinasi [WERKSX, KUNNR, NAME1] untuk tahap delete

            foreach ($T_DATA as $row) {
                $row['WERKSX'] = $kode;
                if (empty($row['EDATU'])) $row['EDATU'] = null;

                // normalisasi kunci
                $kunnr = trim((string)($row['KUNNR'] ?? ''));
                $name1 = trim((string)($row['NAME1'] ?? ''));

                // jika dua-duanya kosong, skip agar tidak ada baris tanpa kunci
                if ($kunnr === '' && $name1 === '') {
                    Log::warning('Skip T_DATA tanpa kunci (KUNNR & NAME1 kosong)', ['row' => $row]);
                    continue;
                }

                // pastikan field kunci ikut tersimpan sesuai normalisasi
                $row['KUNNR'] = $kunnr !== '' ? $kunnr : null;
                $row['NAME1'] = $name1 !== '' ? $name1 : null;

                try {
                    ProductionTData::updateOrCreate(
                        ['WERKSX' => $kode, 'KUNNR' => $row['KUNNR'], 'NAME1' => $row['NAME1']],
                        $row
                    );
                    $keep0[] = [$kode, $row['KUNNR'], $row['NAME1']];
                } catch (\Exception $e) {
                    Log::warning('Gagal simpan T_DATA', ['row' => $row, 'error' => $e->getMessage()]);
                }
            }

            // == T_DATA1
            $keep1 = []; // [ORDERX, VORNR]
            foreach ($T1 as $row) {
                $orderx = $row['ORDERX'] ?? null;
                $vornr  = $row['VORNR'] ?? null;
                if (!$orderx) continue;

                $sssl1 = $formatTanggal($row['SSSLDPV1'] ?? '');
                $sssl2 = $formatTanggal($row['SSSLDPV2'] ?? '');
                $sssl3 = $formatTanggal($row['SSSLDPV3'] ?? '');

                $pv1 = (!empty($row['ARBPL1']) && !empty($sssl1)) ? strtoupper($row['ARBPL1'].' - '.$sssl1) : null;
                $pv2 = (!empty($row['ARBPL2']) && !empty($sssl2)) ? strtoupper($row['ARBPL2'].' - '.$sssl2) : null;
                $pv3 = (!empty($row['ARBPL3']) && !empty($sssl3)) ? strtoupper($row['ARBPL3'].' - '.$sssl3) : null;

                $row['WERKSX'] = $kode;

                ProductionTData1::updateOrCreate(
                    ['ORDERX' => $orderx, 'VORNR' => $vornr],
                    array_merge($row, ['PV1'=>$pv1, 'PV2'=>$pv2, 'PV3'=>$pv3])
                );

                $keep1[] = [$orderx, $vornr];
            }

            // == T_DATA2
            $keep2WithKey = []; $keep2Ids = [];
            forEach ($T2 as $row) {
                $row['WERKSX'] = $kode;
                if (empty($row['EDATU'])) $row['EDATU'] = null;

                try {
                    if (!empty($row['KDAUF']) && !empty($row['KDPOS'])) {
                        ProductionTData2::updateOrCreate(
                            ['WERKSX'=>$kode, 'KDAUF'=>$row['KDAUF'], 'KDPOS'=>$row['KDPOS']],
                            $row
                        );
                        $keep2WithKey[] = [$row['KDAUF'], $row['KDPOS']];
                    } else {
                        $created = ProductionTData2::create($row);
                        $keep2Ids[] = $created->id;
                    }
                } catch (\Exception $e) {
                    Log::warning('Gagal simpan T_DATA2', ['row'=>$row, 'error'=>$e->getMessage()]);
                }
            }

            // == T_DATA3
            $keep3 = []; // [ORDERX, VORNR]
            foreach ($T3 as $row) {
                if (!isset($row['ORDERX'])) continue;
                $row['WERKSX'] = $kode;

                ProductionTData3::updateOrCreate(
                    ['ORDERX'=>$row['ORDERX'], 'VORNR'=>$row['VORNR'] ?? null],
                    $row
                );
                $keep3[] = [$row['ORDERX'], $row['VORNR'] ?? null];
            }

            // == T_DATA4
            $keep4 = []; // [RSNUM, RSPOS]
            foreach ($T4 as $row) {
                if (!isset($row['RSNUM']) || !isset($row['RSPOS'])) continue;
                $row['WERKSX'] = $kode;

                ProductionTData4::updateOrCreate(
                    ['RSNUM'=>$row['RSNUM'], 'RSPOS'=>$row['RSPOS']],
                    $row
                );
                $keep4[] = [$row['RSNUM'], $row['RSPOS']];
            }

            // ==============================
            // 3) DELETE yang tidak terpakai
            // ==============================

            // --- T_DATA (per plant) — bersihkan yang tidak ada di payload
            $existing0 = ProductionTData::where('WERKSX', $kode)->get();
            $toKeep0 = collect($keep0); // elemen: [$WERKSX, $KUNNR, $NAME1]

            foreach ($existing0 as $item) {
                $match = $toKeep0->contains(function ($v) use ($item) {
                    // samakan representasi null/empty
                    $kunnr = $item->KUNNR ?? null;
                    $name1 = $item->NAME1 ?? null;
                    return $v[0] === $item->WERKSX && $v[1] === $kunnr && $v[2] === $name1;
                });

                if (!$match) {
                    $item->delete();
                }
            }

            // --- T_DATA1
            $existing1 = ProductionTData1::where('WERKSX', $kode)->get(); // hindari all()
            $toKeep1 = collect($keep1);
            foreach ($existing1 as $item) {
                if (!$toKeep1->contains(fn($v) => $v[0] === $item->ORDERX && $v[1] === $item->VORNR)) {
                    $item->delete();
                }
            }

            // --- T_DATA2
            $existing2 = ProductionTData2::where('WERKSX', $kode)->get();
            $toKeep2WithKey = collect($keep2WithKey);
            $toKeep2Ids     = collect($keep2Ids);
            foreach ($existing2 as $item) {
                if (!empty($item->KDAUF) && !empty($item->KDPOS)) {
                    if (!$toKeep2WithKey->contains(fn($v)=>$v[0]===$item->KDAUF && $v[1]===$item->KDPOS)) {
                        $item->delete();
                    }
                } else {
                    if (!$toKeep2Ids->contains($item->id)) {
                        $item->delete();
                    }
                }
            }

            // --- T_DATA3
            $existing3 = ProductionTData3::where('WERKSX', $kode)->get();
            $toKeep3 = collect($keep3);
            foreach ($existing3 as $item) {
                if (!$toKeep3->contains(fn($v)=>$v[0]===$item->ORDERX && $v[1]===$item->VORNR)) {
                    $item->delete();
                }
            }

            // --- T_DATA4 (batasi hanya RSNUM yang muncul agar tidak global)
            if (!empty($keep4)) {
                $existing4 = ProductionTData4::whereIn('RSNUM', array_unique(array_map(fn($v)=>$v[0], $keep4)))->get();
                $toKeep4 = collect($keep4);
                foreach ($existing4 as $item) {
                    if (!$toKeep4->contains(fn($v)=>$v[0]===$item->RSNUM && $v[1]===$item->RSPOS)) {
                        $item->delete();
                    }
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

        // Sumber utama: T_DATA (bukan T_DATA2)
        $query = ProductionTData::where('WERKSX', $kode);

        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('KDAUF', 'like', "%{$search}%")
                ->orWhere('KDPOS', 'like', "%{$search}%")
                ->orWhere('MATNR', 'like', "%{$search}%")
                ->orWhere('MAKTX', 'like', "%{$search}%")
                ->orWhere('KUNNR', 'like', "%{$search}%")
                ->orWhere('NAME1', 'like', "%{$search}%");
            });
        }

        $tdata = $query->orderBy('KDAUF')->orderBy('KDPOS')->paginate(10);

        // Kunci untuk data terkait
        $kdaufValues = $tdata->pluck('KDAUF')->filter()->unique();
        $kdposValues = $tdata->pluck('KDPOS')->filter()->unique();

        // === T_DATA2 untuk SO-Item pada halaman ini ===
        $allTData2 = ProductionTData2::where('WERKSX', $kode)
            ->when($kdaufValues->isNotEmpty(), fn($q) => $q->whereIn('KDAUF', $kdaufValues))
            ->when($kdposValues->isNotEmpty(), fn($q) => $q->whereIn('KDPOS', $kdposValues))
            ->get()
            ->groupBy(fn($r) => ($r->KDAUF ?? '').'-'.($r->KDPOS ?? ''));

        // === T_DATA3 terkait (seperti sebelumnya) ===
        $allTData3 = ProductionTData3::where('WERKSX', $kode)
            ->when($kdaufValues->isNotEmpty(), fn($q) => $q->whereIn('KDAUF', $kdaufValues))
            ->when($kdposValues->isNotEmpty(), fn($q) => $q->whereIn('KDPOS', $kdposValues))
            ->get();

        $orderxVornrKeys = $allTData3
            ->map(fn($it) => ($it->ORDERX ?? '').'-'.($it->VORNR ?? ''))
            ->filter()->unique();

        $aufnrValues = $allTData3->pluck('AUFNR')->filter()->unique();
        $plnumValues = $allTData3->pluck('PLNUM')->filter()->unique();

        $allTData1Query = ProductionTData1::query();
        if (Schema::hasColumn((new ProductionTData1)->getTable(), 'WERKSX')) {
            $allTData1Query->where('WERKSX', $kode);
        }
        if ($orderxVornrKeys->isNotEmpty()) {
            $allTData1Query->whereIn(DB::raw("CONCAT(ORDERX, '-', VORNR)"), $orderxVornrKeys->values());
        } else {
            $allTData1Query->whereRaw('1=0');
        }
        $allTData1 = $allTData1Query->get()
            ->groupBy(fn($it) => ($it->ORDERX ?? '').'-'.($it->VORNR ?? ''));

        $allTData4ByAufnr = ProductionTData4::whereIn('AUFNR', $aufnrValues->values())->get()->groupBy('AUFNR');
        $allTData4ByPlnum = ProductionTData4::whereIn('PLNUM', $plnumValues->values())->get()->groupBy('PLNUM');

        $allTData3Grouped = $allTData3->groupBy(fn($it) => ($it->KDAUF ?? '').'-'.($it->KDPOS ?? ''));

        return view('Admin.detail-data2', [
            'plant'            => $kode,
            'categories'       => $kodeInfo->kategori,
            'bagian'           => $kodeInfo->nama_bagian,
            'tdata'            => $tdata,                 // tabel utama = T_DATA
            'allTData2'        => $allTData2,            // <-- baru
            'allTData3'        => $allTData3Grouped,
            'allTData1'        => $allTData1,
            'allTData4ByAufnr' => $allTData4ByAufnr,
            'allTData4ByPlnum' => $allTData4ByPlnum,
            'search'           => $search,
        ]);
    }

    public function convertPlannedOrder(Request $request)
    {
        // dd($request);
        // 1. Validasi input dari frontend
        $validated = $request->validate([
            'PLANNED_ORDER' => 'required|string',
            'AUART' => 'required|string',
            // 'PLANT' => 'required|string',
        ]);
        // 2. Tentukan URL API Python Anda
        try {
            // 3. Kirim request POST ke API Python menggunakan Laravel HTTP Client
            $response = Http::timeout(0)->withHeaders([
                'X-SAP-Username' => session('username'),
                'X-SAP-Password' => session('password'),
            ])->post('http://127.0.0.1:8006/api/create_prod_order',[
                'PLANNED_ORDER' => $validated['PLANNED_ORDER'],
                'AUART' => $validated['AUART'],
                'PLANT' => $request['PLANT'],
            ]);
            // 4. Periksa jika request ke Python gagal
            if ($response->failed()) {
                // Jika gagal, kirim pesan error ke frontend
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal terhubung ke service SAP/Python.',
                    'details' => $response->body() // Opsional: kirim detail error
                ], 502); // 502 Bad Gateway adalah status yang tepat untuk proxy error
            }
            // 5. Jika berhasil, teruskan respons asli dari Python ke frontend
            return $response->json();
        } catch (\Exception $e) {
            // Menangani error jika server Python tidak bisa dihubungi sama sekali
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menjangkau server SAP/Python.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
