<?php

namespace App\Charts\Frontend;

use Illuminate\Support\Carbon;
use App\Models\TransaksiKomisiReferal;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class KomisiAfiliasi
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\BarChart
    {
        $bulanSekarang = Carbon::now()->format('m');
        if ($bulanSekarang <= 6) {
            $month = ['Jan', 'Feb', 'Mar', 'Apr', 'Mey', 'Jun'];
            $data = [
                'name' => 'Pendapatan',
                'data' => [
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::JANUARY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::FEBRUARY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::MARCH)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::APRIL)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::MAY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::JUNE)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi')
                ]
            ];
        } else {
            $month = ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $data = [
                'name' => 'Pendapatan',
                'data' => [
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::JULY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::AUGUST)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::SEPTEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::OCTOBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::NOVEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                    TransaksiKomisiReferal::whereMonth('created_at', Carbon::DECEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi')
                ]
            ];
        }

        return $this->chart->barChart()
            ->setTitle('San Francisco vs Boston.')
            ->setSubtitle('Wins during season 2021.')
            ->addData($data['name'], $data['data'])
            ->setXAxis($month);
    }
}
