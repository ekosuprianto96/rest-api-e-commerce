@extends('layouts.main', ['title' => 'Konfirmasi Pembayaran'])
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
    <h1 style="font-size: 1.5em">Konfirmasi Pembayaran</h1>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>No Order</th>
                <th>Customer</th>
                <th>Total Produk</th>
                <th>Total Biaya</th>
                <th>Total Potongan</th>
                <th>Kode Unique</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if($order->count() > 0) 
                @foreach($order as $key => $value)
                  <tr>
                    <td>
                      {{ $order->firstItem() + $key }}
                    </td>
                    <td>{{ $value->no_order }}</td>
                    <td>{{ $value->user->full_name }}</td>
                    <td>{{ $value->detail->count() }}</td>
                    <td>Rp. {{ number_format($value->total_biaya, 2) }}</td>
                    <td>Rp. {{ number_format($value->total_potongan, 2) }}</td>
                    <td>{{ $value->kode_unique }}</td>
                    <td>
                      <div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                        <a href="{{ route('admin.payment.detail', $value->no_order) }}" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                        <form class="m-0" action="{{ route('admin.payment.konfirmasi', $value->no_order) }}" method="POST">
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
              {{ $order->links() }}
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection