<?php

namespace App\Charts;

use App\Models\Order;
use Illuminate\Support\Carbon;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class ReportOrder
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\BarChart
    {
        $bulanSekarang = Carbon::now()->format('m');
        $tahunSekarang = Carbon::now()->format('Y');
        if (intval($bulanSekarang) <= 6) {
            return $this->chart->barChart()
                ->setTitle('Total Order vs Produk Laku')
                ->setSubtitle("Data Total Order Dan Produk Laku Tahun {$tahunSekarang}")
                ->addData('Total Order', [
                    Order::whereMonth('created_at', Carbon::JANUARY)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::FEBRUARY)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::MARCH)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::APRIL)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::MAY)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::JUNE)->whereYear('created_at', Carbon::now()->format('Y'))->count()
                ])
                ->addData('Produk Laku', [
                    Order::whereMonth('created_at', Carbon::JANUARY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::FEBRUARY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::MARCH)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::APRIL)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::MAY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::JUNE)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity')
                ])
                ->setXAxis(['January', 'February', 'March', 'April', 'May', 'June']);
        } else {
            return $this->chart->barChart()
                ->setTitle('Total Order vs Penghasilan')
                ->setSubtitle("Data Total Order Dan Penghasilan Tahun {$tahunSekarang}")
                ->addData('Total Order', [
                    Order::whereMonth('created_at', Carbon::JULY)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::AUGUST)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::SEPTEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::OCTOBER)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::NOVEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->count(),
                    Order::whereMonth('created_at', Carbon::DECEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->count()
                ])
                ->addData('Produk Laku', [
                    Order::whereMonth('created_at', Carbon::JULY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::AUGUST)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::SEPTEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::OCTOBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::NOVEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity'),
                    Order::whereMonth('created_at', Carbon::DECEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('quantity')
                ])
                ->setXAxis(['Juli', 'Agustus', 'September', 'Oktober', 'November', 'September']);
        }
    }

    public function get_penghasilan($data) {
        $total = 0;

        foreach($data as $d) {
            $biaya_platform = (float) ($d->biaya_platform / 100) * $d->total_biaya;
            $total += $biaya_platform;
        }

        return 'Rp. '.number_format($total, 0);
    }
}
