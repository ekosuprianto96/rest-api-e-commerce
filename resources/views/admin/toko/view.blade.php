@extends('layouts.main', ['title' => 'Detail Toko'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
  }
  tbody tr td, tfoot tr td {
    font-size: 0.8em;
  }
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="border w-100 d-flex align-items-center justify-content-center" style="height: 300px;overflow: hidden;">
              <img src="{{ $toko->image }}" class="w-100" alt="">
            </div>
          </div>
          <div class="col-md-8">
            <div class="row">
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Nama Toko</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $toko->nama_toko }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Nama Pemilik</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $toko->user->full_name }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Alamat Toko</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $toko->alamat_toko }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Email</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $toko->user->email }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">No Hape</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $toko->user->no_hape }}">
              </div>
            </div>
            <div class="col-md-12 px-0 d-flex align-items-center" style="gap: 7px;">
              <a href="{{ isset($url_back) ? $url_back : route('admin.toko.index') }}" class="btn btn-primary">Kembali</a>
              @if($toko->status_toko == 1)
                <form class="m-0" action="{{ route('admin.user.batal-konfirmasi', $toko->kode_toko) }}" method="POST">
                  @method('PUT')
                  @csrf
                  <button class="btn btn-danger" type="submit" id="batal_konfirmasi">Batal Konfirmasi</button>
                </form>
              @elseif($toko->status_toko == 0)
                <form class="m-0" action="{{ route('admin.user.konfirmasi', $toko->kode_toko) }}" method="POST">
                  @method('PUT')
                  @csrf
                  <button class="btn btn-success" type="submit">Konfirmasi</button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div class="row mt-3">
      <div class="col-md-12">
        <h3>Detail</h3>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 mb-3">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="produk-tab" data-toggle="tab" data-target="#produk" type="button" role="tab" aria-controls="produk" aria-selected="true">Produk</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="order-tab" data-toggle="tab" data-target="#order" type="button" role="tab" aria-controls="order" aria-selected="true">Order</button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="produk" role="tabpanel" aria-labelledby="produk-tab">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Diskon</th>
                        <th>Terjual</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($produk = App\Models\Produk::where('kode_toko', $toko->kode_toko)->paginate(10) as $key => $value)
                        <tr>
                          <td>{{ $produk->firstItem() + $key }}</td>
                          <td><a href="{{ route('admin.produk.view-produk', $value->kode_produk) }}">{{ $value->nm_produk }}</a></td>
                          <td>{{ $value->kategori->nama_kategori }}</td>
                          <td>Rp. {{ number_format($value->getHargaFixed(), 0) }}</td>
                          <td>Rp. {!! $value->getHargaDiskon()['harga_diskon'] !!}</td>
                          <td>{{ $value->order->count() }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="order" role="tabpanel" aria-labelledby="order-tab">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>No Order</th>
                        <th>Customer</th>
                        <th>Type Produk</th>
                        <th>Total Biaya</th>
                        <th>Total Potongan</th>
                        <th>Status Order</th>
                        <th>Status Pembayaran</th>
                        <th>Tanggal</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $total_pendapatan = 0;
                        $total_potongan = 0;
                      @endphp
                      @foreach($order = App\Models\DetailOrder::where('kode_toko', $toko->kode_toko)->paginate(10) as $key => $value)
                        <tr>
                          <td>{{ $order->firstItem() + $key }}</td>
                          <td>{{ $value->no_order }}</td>
                          <td>{{ $value->user->full_name }}</td>
                          <td>{{ $value->type_produk }}</td>
                          <td>Rp. {{ number_format($value->total_biaya, 0) }}</td>
                          <td>Rp. {{ number_format($value->potongan, 0) }}</td>
                          <td>
                            @if($value->status_order == 'SUCCESS')
                              <span class="badge badge-sm badge-success">{{ $value->status_order }}</span>
                            @elseif($value->status_order == 'PENDING')
                              <span class="badge badge-sm badge-warning">{{ $value->status_order }}</span>
                            @else
                              <span class="badge badge-sm badge-danger">{{ $value->status_order }}</span>
                            @endif
                          </td>
                          <td>
                            @if($value->order->status_order == 'SUCCESS')
                              <span class="badge badge-sm badge-success">{{ $value->order->status_order }}</span>
                            @elseif($value->order->status_order == 'PENDING')
                              <span class="badge badge-sm badge-warning">{{ $value->order->status_order }}</span>
                            @else
                              <span class="badge badge-sm badge-danger">{{ $value->order->status_order }}</span>
                            @endif
                          </td>
                          <td>{{ $value->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @php
                          $total_pendapatan += $value->total_biaya;
                          $total_potongan += $value->potongan;
                        @endphp
                      @endforeach
                    </tbody>
                    <tfoot style="background-color: rgb(207, 207, 207);">
                      <tr>
                        <td>Total :</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Rp. {{ number_format($total_pendapatan, 0) }}</td>
                        <td>Rp. {{ number_format($total_potongan, 0) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                  <div>
                    {{ $order->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection