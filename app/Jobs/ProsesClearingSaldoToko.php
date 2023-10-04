<?php

namespace App\Jobs;

use App\Models\SaldoToko;
use App\Models\SaldoRefaund;
use App\Models\ClearingSaldo;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;

class ProsesClearingSaldoToko implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $daftar_clearing = ClearingSaldo::where('jadwal_clear', '<=', now()->format('Y-m-d'))->get();

        if($daftar_clearing->count() > 0) {
            foreach($daftar_clearing as $daftar) {
                // Lakukan pemindahan saldo clearing ke saldo utama di sini
                $saldo_utama = SaldoToko::where('kode_toko', $daftar->kode_toko)->first();
                $saldo_clearing = SaldoRefaund::where('kode_toko', $daftar->kode_toko)->first();
                // Pindahkan Saldo
                $saldo_utama->addSaldo($daftar->kode_toko, $daftar->saldo);
                $saldo_clearing->total_refaund = 0;
                $saldo_clearing->save();
                
                $daftar->delete();
    
                Log::info('Berhasil Mutasi Saldo Toko :'.$saldo_utama->toko->nama_toko);
            }
        }else {
            Log::info("Tida Ada Jadwal Mutasi Saldo Toko.");
        }
    }
}
