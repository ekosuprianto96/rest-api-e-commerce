<?php

namespace App\Console\Commands;

use App\Models\ClearingSaldo;
use App\Models\SaldoRefaund;
use App\Models\SaldoToko;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class ProsesClearingSaldoToko extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proses:clearing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proses Clearing Saldo Penjual Ke Saldo Utama';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 
    }
}
