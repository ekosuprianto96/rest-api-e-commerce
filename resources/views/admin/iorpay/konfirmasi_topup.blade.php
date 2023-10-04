@extends('layouts.main', ['title' => 'Konfirmasi Topup'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
    text-align: center;
  }
  tbody tr td {
    font-size: 0.8em;
    text-align: center;
  }
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <h1 style="font-size: 1.5em">Konfirmasi Topup</h1>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>No Transaksi</th>
                <th>Customer</th>
                <th>Total Permintaan</th>
                <th>Total Di Transfer</th>
                <th>Biaya Admin</th>
                <th>Kode Unique</th>
                <th>Tanggal</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if($trx_topup->count() > 0) 
                @foreach($trx_topup as $key => $value)
                  <tr>
                    <td>
                      {{ $trx_topup->firstItem() + $key }}
                    </td>
                    <td>{{ $value->no_trx }}</td>
                    <td>{{ $value->user->full_name }}</td>
                    <td>Rp. {{ number_format($value->total_fixed, 2) }}</td>
                    <td>Rp. {{ number_format($value->total_trx, 2) }}</td>
                    <td>Rp. {{ number_format($value->biaya_adm, 2) }}</td>
                    <td>{{ $value->kode_unique }}</td>
                    <td>{{ $value->created_at->format('d/m/Y') }}</td>
                    <td>
                      <div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                        <a href="{{ route('admin.payment.detail', $value->no_trx) }}" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                        <form class="m-0" action="{{ route('admin.iorpay.konfirmasi', $value->no_trx) }}" method="POST">
                          @method('PUT')
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="ri-checkbox-circle-fill"></i> Konfirmasi</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else 
                  <tr>
                    <td colspan="11" align="center" class="p-4">Tidak Ada Data</td>
                  </tr>
              @endif
            </tbody>
            <tfoot>
              {{ $trx_topup->links() }}
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection