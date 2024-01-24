@extends('layouts.main', ['title' => 'Detail Transaksi'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
  }
  tbody tr td {
    font-size: 0.8em;
  }
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6 mb-3">
        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-danger">Kembali</a>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">Detail User</h4>
        <div class="row">
          <div class="col-md-3">
            <img src="{{ $order->user->image }}" class="w-100" alt="">
          </div>
          <div class="col-md-9">
            <table>
              <tr>
                <td class="px-3 py-2"><strong>Nama User</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->user->full_name }}</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Email</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->user->email }}</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>No Hape</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->user->no_hape }}</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Alamat</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->user->alamat }}</strong></td>
              </tr>
            </table>
          </div>
        </div>
        <hr>
        <h4 class="mb-3 mt-4">Detail Transaksi</h4>
        <div class="row">
          <div class="col-md-12 border rounded">
            <table style="font-size: 1.2em">
              <tr>
                <td class="px-3 py-2"><strong>No Order</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->no_order }}</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Tanggal Order</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->created_at->format('d-m-Y') }}</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Quantity</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->quantity }} Produk</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Total Potongan</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>Rp. {{ number_format($order->total_potongan, 0) }}</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Total Biaya</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>Rp. {{ number_format($order->total_biaya, 0) }}</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Biaya Platform</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong class="text-danger">-{{ intval($order->biaya_platform) }}%</strong></td>
              </tr>
              <tr>
                <td class="px-3 py-2"><strong>Type Payment</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->type_payment }}</strong></td>
              </tr>
              @if($order->type_payment == 'manual') 
              <tr>
                <td class="px-3 py-2"><strong>Payment Method</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2"><strong>{{ $order->payment->payment_name }}</strong></td>
              </tr>
              @endif
              <tr>
                <td class="px-3 py-2"><strong>Status Pembayaran</strong></td>
                <td class="px-3 py-2"><strong>:</strong></td>
                <td class="px-3 py-2">
                  @if($order->status_order == 'SUCCESS')
                    <span class="badge badge-sm badge-success">{{ $order->status_order }}</span>
                  @elseif($order->status_order == 'PENDING')
                    <span class="badge badge-sm badge-warning">{{ $order->status_order }}</span>
                  @elseif($order->status_order == 'CANCEL')
                    <span class="badge badge-sm badge-danger">{{ $order->status_order }}</span>
                  @else
                    <span class="badge badge-sm badge-danger">Belum Bayar</span>
                  @endif
                </td>
              </tr>
            </table>
          </div>
        </div>
        <h4 class="mb-3 mt-4">Produk Yang Dibeli</h4>
        <div class="row">
          <div class="col-md-12">
            <table class="w-100 table" id="table-produk">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Image</th>
                  <th>Nama Produk</th>
                  <th>Kategori</th>
                  <th>Type Produk</th>
                  <th>Harga Awal</th>
                  <th>Harga Diskon</th>
                  <th>Total Diskon</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $('#summernote').summernote({
      heigt: 300,
      minHeight: 300,
      maxHeight: 300
    });

    table_produk = $('#table-produk').DataTable({
      processing: true,
      serverSide: true,
      searching: false,
      paginate: false,
      ajax: {
        method: 'POST',
        url: '{{ route("admin.transaksi.data-transaksi-produk") }}',
        data: {
          no_order: '{{ $order->no_order }}',
          _token: '{{ csrf_token() }}'
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'image', search: true, name: 'image'},
        { data: 'nama_produk', search: true, name: 'nama_produk'},
        { data: 'kategori', search: true, name: 'kategori'},
        { data: 'type_produk', search: true, name: 'kategori'},
        { data: 'harga_real', search: true, name: 'harga_real'},
        { data: 'harga_fixed', search: true, name: 'harga_fixed'},
        { data: 'total_diskon', search: true, name: 'total_diskon'},
        { data: 'action', name: 'action'},
      ]
    });
  });
</script>
@endsection