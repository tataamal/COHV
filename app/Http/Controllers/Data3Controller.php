<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use App\Models\ProductionTData3;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Data3Controller extends Controller
{
    public function releaseOrderDirect(Request $request, $aufnr) {
        $payload = ["AUFNR" => $aufnr];

        $response = Http::timeout(0)->withHeaders([
            'X-SAP-Username' => session('username'),
            'X-SAP-Password' => session('password'),
        ])->post('http://127.0.0.1:8006/api/release_order', $payload);

        if ($response->successful()) {
            $data = $response-> json();
            $return = $data['RETURN'] ?? $data['BAPI_PRODORD_RELEASE']['RETURN'] ?? [];
            $message = is_array($return) ? ($return[0]['MESSAGE'] ?? 'Order berhasil direlease') : 'Order berhasil direlease';

            ProductionTData3::where('AUFNR', $aufnr)->update(['STATS' => 'REL']);

            // Jika request dari fetch() (AJAX), kirim response JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Order $aufnr berhasil direlease. Pesan SAP: {$message}"
                ]);
            }

            // Kalau bukan dari fetch, kembalikan redirect biasa
            return back()->with('success', "Order $aufnr berhasil direlease. Pesan SAP: {$message}");
        }

        $errorMessage = $response->json('error') ?? 'Tidak diketahui';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "Order $aufnr gagal direlease: $errorMessage"
            ], 500);
        }

        return back()->with('error', "Order $aufnr gagal direlease: $errorMessage");

    }

    public function store(Request $request)
    {
        // 1) Validasi input dari form
        $data = $request->validate([
            'aufnr' => ['required','string','max:20'],
            'date'  => ['required','date'],                  // YYYY-MM-DD
            'time'  => ['required','regex:/^\d{2}\.\d{2}\.\d{2}$/'], // HH.MM.SS
        ]);

        // 2) Normalisasi data
        $aufnr       = str_pad($data['aufnr'], 12, '0', STR_PAD_LEFT);         // 12 digit
        $timeColon   = str_replace('.', ':', $data['time']);                   // HH:MM:SS
        $dateYmd     = Carbon::parse($data['date'])->format('Ymd');            // YYYYMMDD
        $humanDt     = Carbon::createFromFormat('Y-m-d H:i:s', "{$data['date']} {$timeColon}")
                           ->format('d-m-Y H:i:s');

        // 5) Payload ke Flask
        $payload = [
            'AUFNR' => $aufnr,
            'DATE'  => $dateYmd,     // YYYYMMDD
            'TIME'  => $timeColon,   // HH:MM:SS
        ];

        try {
            // 6) Call Flask API
            $resp = Http::withHeaders([
                        'X-SAP-Username' => session('username'),
                        'X-SAP-Password' => session('password'),
                    ])->timeout(30)->post('http://127.0.0.1:8006/api/schedule_order', $payload);

            if ($resp->failed()) {
                $msg = 'Gagal terhubung ke SAP Scheduler.';
                $json = $resp->json();
                if (is_array($json) && isset($json['error'])) {
                    $msg = $json['error'];
                } else {
                    $msg = $resp->body();
                }
                return back()->withErrors(['sap' => trim($msg) ?: 'Request gagal.'])->withInput();
            }

            $json = $resp->json();

            // 7) Evaluasi RETURN dari SAP (cek E/A error)
            $sapReturn = $json['sap_return'] ?? [];
            $errors = collect($sapReturn)
                ->filter(function ($r) {
                    $t = strtoupper($r['TYPE'] ?? '');
                    return in_array($t, ['E','A']); // Error / Abort
                })
                ->map(function ($r) {
                    $id = $r['ID'] ?? '';
                    $no = $r['NUMBER'] ?? '';
                    $msg= $r['MESSAGE'] ?? 'Error';
                    return trim("$msg (ID:$id NO:$no)");
                })
                ->values();

            if ($errors->isNotEmpty()) {
                // Ada error dari BAPI
                return back()->withErrors(['sap' => $errors->implode("\n")])->withInput();
            }

            // 8) Ambil info sukses (opsional, dari DETAIL_RETURN / APPLICATION_LOG)
            $infos = collect($sapReturn)
                ->filter(fn($r) => strtoupper($r['TYPE'] ?? '') === 'S')
                ->pluck('MESSAGE')
                ->filter()
                ->values();

            $detail = collect($json['detail_return'] ?? [])->pluck('MESSAGE')->filter()->values();
            $applog = collect($json['application_log'] ?? [])->pluck('MESSAGE')->filter()->values();

            $extraMsg = $infos->merge($detail)->merge($applog)->implode(' | ');

            return back()->with('success',
                "Jadwal PRO {$aufnr} tersimpan di SAP: {$humanDt}".($extraMsg ? " — {$extraMsg}" : '')
            );

        } catch (\Throwable $e) {
            return back()->withErrors([
                'sap' => 'Exception: '.$e->getMessage()
            ])->withInput();
        }
    }

}
