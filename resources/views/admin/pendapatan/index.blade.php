@extends('layouts.main', ['title' => 'Pendapatan'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
    text-align: center;
  }
  tbody tr td {
    font-size: 0.8em;
  }
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <h1 style="font-size: 1.5em">Detail Pendapatan</h1>
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Belum Dibayar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{ number_format(\App\Models\Pendapatan::where([
                                'status' => 'PENDING',
                                'type_payment' => 'DEBIT'
                            ])->sum('pendapatan'), 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pendapatan Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{ number_format(\App\Models\Pendapatan::where([
                                'status' => 'SUCCESS',
                                'type_payment' => 'DEBIT'
                            ])->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".\Carbon\carbon::now()->firstOfMonth()->format('Y-m-d')."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".\Carbon\carbon::now()->lastOfMonth()->format('Y-m-d')."'")->sum('pendapatan'), 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pendapatan Bersih
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{ number_format(\App\Models\Pendapatan::where([
                                'status' => 'SUCCESS',
                                'type_payment' => 'DEBIT'
                            ])->sum('pendapatan'), 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Semua Pendapatan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{ number_format(\App\Models\Pendapatan::sum('pendapatan'), 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.pendapatan') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="">Mulai Tanggal</label>
                    <input name="tanggal_mulai" id="tanggal_muali" type="date" value="{{ \Carbon\carbon::now()->firstOfMonth()->format('Y-m-d') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label for="">Sampai Tanggal</label>
                    <input name="tanggal_akhir" id="tanggal_akhir" type="date" value="{{ \Carbon\carbon::now()->lastOfMonth()->format('Y-m-d') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label for="">Type</label>
                    <select name="type" id="" class="form-control form-control-sm">
                        <option value="">-- Semua Type --</option>
                        <option value="manual">Manual Transfer</option>
                        <option value="gateway">Gateway</option>
                        <option value="iorpay">Pay</option>
                        {{-- <option value=""></option> --}}
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="w-100 h-100 d-flex align-items-end" style="gap: 5px;">
                        <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                        <a href="{{ route('admin.pendapatan') }}" class="btn btn-sm btn-warning">Refresh</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No Transaksi</th>
                        <th>Type</th>
                        <th>Type Pembayaran</th>
                        <th>Biaya</th>
                        <th>Total</th>
                        <th>No Refrensi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_pendapatan_semua = 0;
                    @endphp
                    @foreach($group_pd as $key => $value)
                        @php
                            $nama1 = 'total_biaya';
                            $nama2 = 'total_pendapatan';
                            ${$nama1.$key} = 0;
                            ${$nama2.$key} = 0;
                        @endphp
                        <tr>
                            <td colspan="8" class="bg-warning text-light" style="font-weight: bold;">{{ Str::upper($key) }}</td>
                        </tr>
                        @foreach($value as $key_2 => $data)
                            @php
                                ${$nama1.$key} += (float) intval($data->biaya);
                                ${$nama2.$key} += (float) intval($data->pendapatan);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $data->no_trx }}</td>
                                <td class="text-center">{{ $data->type }}</td>
                                <td class="text-center">{{ ($data->payment != null ? $data->payment->payment_name : $data->account) }}</td>
                                <td class="text-right">Rp. {{ number_format($data->biaya, 0) }}</td>
                                @if($data->type_payment == 'DEBIT')
                                <td class="text-right text-success">+Rp. {{ number_format($data->pendapatan, 0) }}</td> 
                                @else
                                <td class="text-right text-danger">-Rp. {{ number_format($data->pengeluaran, 0) }}</td>     
                                @endif
                                <td class="text-center">{{ $data->no_refrensi }}</td>
                                <td class="text-center">
                                    @if($data->status == 'SUCCESS')
                                    <span class="badge badge-sm badge-success">
                                        {{ $data->status }}
                                    </span>
                                    @elseif($data->status == 'PENDING')
                                    <span class="badge badge-sm badge-warning">
                                        {{ $data->status }}
                                    </span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $data->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="bg-danger text-light">Total :</td>
                            <td class="bg-danger text-light"></td>
                            <td class="bg-danger text-light"></td>
                            <td class="text-right bg-danger text-light">Rp. {{ number_format(${$nama1.$key}, 0) }}</td>
                            <td class="text-right bg-danger text-light">Rp. {{ number_format(${$nama2.$key}, 0) }}</td>
                            <td class="bg-danger text-light"></td>
                            <td class="bg-danger text-light"></td>
                            <td class="bg-danger text-light"></td>
                        </tr>
                        @php
                            $total_pendapatan_semua += ${$nama2.$key};
                        @endphp
                    @endforeach
                    {{-- <tr>
                        <td></td>
                    </tr> --}}
                </tbody>
                <tfoot>
                    <tr>
                        <td class="bg-success text-light">Total Pendapatan :</td>
                        <td class="bg-success text-light"></td>
                        <td class="bg-success text-light"></td>
                        <td class="bg-success text-light"></td>
                        {{-- <td class="text-right bg-success text-light">Rp. {{ number_format(${$nama2.$key}, 0) }}</td> --}}
                        <td class="text-right bg-success text-light">Rp. {{ number_format($total_pendapatan_semua, 0) }}</td>
                        <td class="bg-success text-light"></td>
                        <td class="bg-success text-light"></td>
                        <td class="bg-success text-light"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection