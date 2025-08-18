<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kode;
use App\Models\SapUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Client\ConnectionException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function getSapUserByKode(Request $request)
    {
        $request->validate(['kode_admin' => 'required|string']);
        $kode = Kode::with('sapUser')->where('kode', $request->input('kode_admin'))->first();

        if ($kode && $kode->sapUser) {
            return response()->json(['sap_user_id' => $kode->sapUser->sap_id]);
        }
        return response()->json(['message' => 'Kode tidak ditemukan'], 404);
    }

    public function loginKorlap(Request $request)
    {
        $validated = $request->validate([
            'kode_korlap' => 'required|string',
            'nik' => 'required|numeric',
            'sap_user_id' => 'required|string',
            'sap_password' => 'required|string',
        ]);

        try {
            $kode = Kode::with('sapUser')->where('kode', $validated['kode_korlap'])->first();

            // Validasi internal sebelum menghubungi SAP
            if (!$kode || $kode->nik != $validated['nik'] || !$kode->sapUser || $kode->sapUser->sap_id !== $validated['sap_user_id']) {
                return back()->withErrors(['login' => 'Kombinasi Kode, NIK, dan SAP User ID tidak valid.']);
            }

            // --- INTEGRASI API FLASK DIMULAI ---
            $response = Http::timeout(30)->post('http://127.0.0.1:8006/api/sap-login', [
                'username' => $validated['sap_user_id'],
                'password' => $validated['sap_password'],
            ]);

            // Jika otentikasi di SAP gagal
            if (!$response->successful()) {
                $errorMessage = $response->json('message', 'Username atau Password SAP tidak valid.');
                return back()->withErrors(['login' => $errorMessage]);
            }

            session(['username' => $validated['sap_user_id']]);
            session(['password' => $validated['sap_password']]);


            // --- INTEGRASI API FLASK SELESAI ---

            // Jika otentikasi SAP berhasil, lanjutkan membuat user di Laravel
            $user = User::firstOrCreate(
                ['email' => $validated['nik'] . '@kmi.local'], // Buat email unik
                [
                    'name' => $kode->sapUser->nama,
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'korlap'
                ]
            );

            Auth::login($user, true);
            
            // JIKA BERHASIL: Redirect ke dashboard korlap
            return redirect()->route('korlap.dashboard');

        } catch (ConnectionException $e) {
            Log::error('Koneksi ke API SAP Gagal: ' . $e->getMessage());
            return back()->withErrors(['login' => 'Tidak dapat terhubung ke layanan otentikasi. Hubungi administrator.']);
        }
    }

    public function loginAdmin(Request $request)
    {
        $validated = $request->validate([
            'sap_id' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // --- INTEGRASI API FLASK DIMULAI ---
            $response = Http::timeout(30)->post('http://127.0.0.1:8006/api/sap-login', [
                'username' => $validated['sap_id'],
                'password' => $validated['password'],
            ]);

            session([
                'username' => $validated['sap_id'],
                'password' => $validated['password'],
            ]);

            // Jika otentikasi di SAP gagal
            if (!$response->successful()) {
                $errorMessage = $response->json('message', 'Username atau Password SAP tidak valid.');
                return back()->withErrors(['login' => $errorMessage]);
            }
            // --- INTEGRASI API FLASK SELESAI ---

            // Jika otentikasi SAP berhasil, lanjutkan membuat user di Laravel
            $sapUser = SapUser::where('sap_id', $validated['sap_id'])->first();
            if (!$sapUser) {
                return back()->withErrors(['login' => 'SAP ID ini tidak terdaftar di sistem internal.']);
            }

            $user = User::firstOrCreate(
                ['email' => $validated['sap_id'] . '@kmi.local'],
                [
                    'name' => $sapUser->nama,
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'admin'
                ]
            );
            
            if ($user->role !== 'admin') {
                $user->role = 'admin';
                $user->save();
            }

            Auth::login($user, true);
            
            // JIKA BERHASIL: Redirect ke dashboard admin
            return redirect()->route('admin.dashboard');

        } catch (ConnectionException $e) {
            Log::error('Koneksi ke API SAP Gagal: ' . $e->getMessage());
            return back()->withErrors(['login' => 'Tidak dapat terhubung ke layanan otentikasi. Hubungi administrator.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
