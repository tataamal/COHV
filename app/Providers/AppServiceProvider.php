<?php

namespace App\Providers;

use Illuminate\Support\Facades\View; // Import View Facade
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;    // Import Auth Facade
use App\Models\Kode;                  // Import model Kode
use App\Models\SapUser;                 // Import model SapUser

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.navigation.sidebar', function ($view) {
            $menuItems = [];

            if (Auth::check()) {
                $user = Auth::user();
                $sapUser = null;

                // --- LOGIKA BARU UNTUK MENCARI SAP USER ---
                if ($user->role === 'admin') {
                    // 1. Ambil 'sap_id' dari email user
                    $sapId = str_replace('@kmi.local', '', $user->email);
                    // 2. Cari SapUser berdasarkan sap_id
                    $sapUser = SapUser::where('sap_id', $sapId)->first();
                } 
                elseif ($user->role === 'korlap') {
                    // 1. Ambil 'nik' dari email user
                    $nik = str_replace('@kmi.local', '', $user->email);
                    // 2. Cari record Kode berdasarkan NIK
                    //    PENTING: Kita asumsikan satu NIK hanya terhubung ke satu SapUser,
                    //    jadi kita ambil record pertama yang ditemukan.
                    $kode = Kode::where('nik', $nik)->first();
                    
                    // 3. Jika ditemukan, ambil relasi SapUser-nya
                    if ($kode) {
                        $sapUser = $kode->sapUser; // Mengambil SapUser melalui relasi di model Kode
                    }
                }
                // --- AKHIR LOGIKA BARU ---

                // Jika SapUser berhasil ditemukan, buat menu dinamisnya
                if ($sapUser) {
                    $submenuItems = [];
                    // Ambil semua 'kodes' yang berelasi dengan SapUser tersebut
                    $allKodes = $sapUser->kode()->get(); 

                    if ($allKodes->isNotEmpty()) {
                        foreach ($allKodes as $kode) {
                            $submenuItems[] = [
                                'name' => $kode->nama_bagian,
                                'route' => '#', // Ganti dengan route yang relevan
                                'badge' => $kode->kategori,
                            ];
                        }
                    }

                    // Buat struktur menu dinamis
                    $menuItems = [
                        [
                            'title' => 'Semua Task',
                            'items' => [
                                [
                                    'name' => 'Manufaktur',
                                    'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                                    'submenu' => $submenuItems
                                ]
                            ]
                        ]
                    ];
                }
            }
            
            $view->with('menuItems', $menuItems);
        });
    }
}
