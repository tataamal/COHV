<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionTData1;
use App\Models\ProductionTData2;
use App\Models\ProductionTData3;
use App\Models\ProductionTData4;

class SyncSapData implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $plantCode;
    /**
     * Create a new job instance.
     */
    public function __construct($plantCode)
    {
        $this->plantCode = $plantCode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. Panggil API Python/Flask Anda
            $response = Http::timeout(300) // Set timeout 5 menit
                ->get('http://alamat_api_python_anda/api/sap_combined', [
                    'plant' => $this->plantCode,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // 2. Mulai transaksi database untuk menjaga konsistensi data
                DB::transaction(function () use ($data) {
                    
                    // Hapus data lama (opsional, jika ingin full refresh)
                    // ProductionTData2::where('WERKSX', $this->plantCode)->delete(); 
                    // Lakukan hal yang sama untuk tabel lain jika perlu
                    
                    // 3. Proses dan simpan setiap tabel data
                    if (!empty($data['T_DATA1'])) {
                        foreach ($data['T_DATA1'] as $item) {
                            ProductionTData1::updateOrCreate(
                                ['AUFNR' => $item['AUFNR'], 'VORNR' => $item['VORNR']], // Kunci unik untuk mencari
                                $item  // Seluruh data untuk di-insert atau di-update
                            );
                        }
                    }

                    if (!empty($data['T_DATA2'])) {
                        foreach ($data['T_DATA2'] as $item) {
                            ProductionTData2::updateOrCreate(
                                ['KDAUF' => $item['KDAUF'], 'KDPOS' => $item['KDPOS']],
                                $item
                            );
                        }
                    }

                    if (!empty($data['T_DATA3'])) {
                        foreach ($data['T_DATA3'] as $item) {
                            ProductionTData3::updateOrCreate(
                                ['AUFNR' => $item['AUFNR']],
                                $item
                            );
                        }
                    }
                    
                    if (!empty($data['T_DATA4'])) {
                        foreach ($data['T_DATA4'] as $item) {
                            ProductionTData4::updateOrCreate(
                                ['RSNUM' => $item['RSNUM'], 'RSPOS' => $item['RSPOS']],
                                $item
                            );
                        }
                    }
                });

                Log::info('Sinkronisasi data SAP untuk plant ' . $this->plantCode . ' berhasil.');

            } else {
                Log::error('Gagal mengambil data dari API SAP: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Terjadi error saat sinkronisasi SAP: ' . $e->getMessage());
        }
    }
}
