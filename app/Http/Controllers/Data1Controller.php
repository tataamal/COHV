<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class Data1Controller extends Controller
{
    public function changeWc(Request $request)
    {
        $request->validate([
            'aufnr'       => 'required|string', // AUFNR 12 digit
            'vornr'       => 'required|string', // VORNR JANGAN di-trim nolnya (0010, 0020, dst)
            'work_center' => 'required|string',
            'sequ'        => 'nullable|string',
        ]);

        $payload = [
            'IV_AUFNR'     => $request->aufnr,
            'IV_COMMIT'    => 'X',
            'IT_OPERATION' => [[
                'SEQUEN'   => $request->sequ ?: '0',
                'OPER'     => $request->vornr,          // kirim apa adanya (termasuk leading zero)
                'WORK_CEN' => $request->work_center,
                'W'        => 'X',
            ]],
        ];

        try {
            $resp = Http::withHeaders([
                    'X-SAP-Username' => session('username'),
                    'X-SAP-Password' => session('password'),
                ])
                ->timeout(30)
                ->post(env('FLASK_BASE_URL', 'http://127.0.0.1:8006').'/api/save_edit', $payload);

            if (!$resp->successful()) {
                return back()->with('error', $resp->json('error') ?? 'Gagal mengubah Work Center di SAP.');
            }

            return back()->with('success', 'Work Center berhasil diubah.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changePv(Request $request)
    {
        
        $data = $request->validate([
            'AUFNR'        => ['required', 'string'],
            'PROD_VERSION' => ['required', 'string'],
        ]);

        $aufnr = trim($data['AUFNR']);
        $verid = trim($data['PROD_VERSION']);

        try {
            $username = session('username');
            $password = session('password');

            if (!$username || !$password) {
                return response()->json(['error' => 'Kredensial SAP tidak ditemukan di session.'], 401);
            }

            $flaskUrl = 'http://127.0.0.1:8006/api/change_prod_version';

            $resp = Http::timeout(60)
                ->withHeaders([
                    'X-SAP-Username' => $username,
                    'X-SAP-Password' => $password,
                ])
                ->post($flaskUrl, [
                    'AUFNR'        => $aufnr,
                    'PROD_VERSION' => $verid,
                ]);

            if (!$resp->successful()) {
                // coba ambil pesan error dari body
                $msg = $resp->json('error') ?? $resp->body();
                return response()->json(['error' => 'Flask error: '.$msg], $resp->status());
            }

            // forward response Flask apa adanya (before_version, after_version, sap_return)
            return response()->json($resp->json(), 200);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Exception: '.$e->getMessage()], 500);
        }
    }
}
