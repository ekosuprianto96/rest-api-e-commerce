@extends('layouts.main', ['title' => 'Detail Order'])
<style>
  /* thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
  }
  tbody tr td {
    font-size: 0.8em;
  } */
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h1 style="font-size: 1.5em">Detail Order</h1>
        </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="w-100 border">
                    <img src="{{ $order->produk->image }}" class="img-fluid" alt="{{ $order->produk->nm_produk }}">
                </div>
            </div>
            <div class="col-md-9">
                <div class="w-100">
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="px-2">Nama Produk</td>
                                <td class="px-2">:</td>
                                <td class="px-2">{{ $order->produk->nm_produk }}</td>
                            </tr>
                            <tr>
                                <td class="px-2">Type Produk</td>
                                <td class="px-2">:</td>
                                <td class="px-2">{{ $order->produk->type_produk }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-1">Kategori</td>
                                <td class="px-2 py-1">:</td>
                                <td class="px-2 py-1">{{ $order->produk->kategori->nama_kategori }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-1">Harga Awal</td>
                                <td class="px-2 py-1">:</td>
                                <td class="px-2 py-1">Rp. {{ number_format($order->produk->harga, 0) }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-1">Harga Fixed</td>
                                <td class="px-2 py-1">:</td>
                                <td class="px-2 py-1">Rp. {{ number_format($order->produk->getHargaFixed(), 0) }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-1">Diskon</td>
                                <td class="px-2 py-1">:</td>
                                <td class="px-2 py-1">Rp. {{ $order->produk->getHargaDiskon()['harga_diskon'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if($order->produk->type_produk == 'MANUAL')
        <div class="row">
            <div class="col-md-12 mt-3">
                <h4>Detail Order</h4>
                @if($order->produk->type_produk == 'MANUAL')
                    <table>
                        <tbody>
                            <tr>
                                <td class="px-2 py-1">Waktu Proses</td>
                                <td class="px-2 py-1">:</td>
                                <td class="px-2 py-1">{{ isset($order->waktu_proses) ? $order->waktu_proses->waktu_proses : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-1">Catatan Penjual</td>
                                <td class="px-2 py-1">:</td>
                                <td class="px-2 py-1">{{ isset($order->waktu_proses) ? $order->waktu_proses->catatan : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="my-3">
                        @if($order->file_pesanan) 
                            @if($order->file_pesanan->file != null)
                            <button class="btn btn-success btn-sm">Download File</button>
                            @else
                            <button class="btn btn-success btn-sm">View Text</button>
                            @endif
                        @else
                            <div class="alert alert-danger">
                                <span>Belum Ada Data</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12 mt-3">
                <h4>Detail Pembayaran</h4>
                <ul style="list-style: none;" class="m-0 p-0 w-100">
                    <li class="w-100 d-flex mb-3 justify-content-between" style="border-bottom: 1px solid">
                        <span>Biaya : </span>
                        <span>Rp. {{ number_format($order->produk->getHargaFixed()) }}</span>
                    </li>
                    <li class="w-100 d-flex mb-3 justify-content-between" style="border-bottom: 1px solid">
                        <span>Potongan Afiliate : </span>
                        <span>Rp. {{ number_format($order->potongan_referal, 0) }}</span>
                    </li>
                    <li class="w-100 d-flex mb-3 justify-content-between" style="border-bottom: 1px solid">
                        <span>Potongan Platform : </span>
                        <span>Rp. {{ number_format($order->potongan_platform, 0) }}</span>
                    </li>
                    <li class="w-100 d-flex mb-3 justify-content-between">
                        <span>Total : </span>
                        <span>Rp. {{ number_format($order->total_biaya, 0) }}</span>
                    </li>
                    <li class="w-100 d-flex mb-3 justify-content-between">
                        <div class="alert alert-success w-100">
                            <span>Pendapatan Perusahaan : </span>
                            <span>+Rp. {{ number_format($order->potongan_platform, 0) }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="d-flex align-items-center MT-3">
                <a href="{{ route('admin.order.daftar-order') }}" class="btn btn-danger">Kembali</a>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
  
</script>
@endsection