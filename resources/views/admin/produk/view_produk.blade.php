@extends('layouts.main', ['title' => 'Daftar Produk Belum Di Konfirmasi'])
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
    <h1 style="font-size: 1.5em">{{ \Str::upper($produk->nm_produk) }}</h1>
    <div class="card">
      <div class="card-body">
        <h4>Detail Toko</h4>
        <div class="row mb-3">
          <div class="col-md-3">
            <img class="w-100" src="{{ $produk->toko->image }}" alt="">
          </div>
          <div class="col-md-9">
            <table class="w-100">
              <tr>
                <td style="width: 200px;padding: 5px 0px;"><strong>Nama Toko</strong></td>
                <td style="padding: 5px 0px;"><strong>:</strong></td>
                <td style="padding: 5px 0px;"><strong>{{ $produk->toko->nama_toko }}</strong></td>
              </tr>
              <tr>
                <td style="width: 200px;padding: 5px 0px;"><strong>Nama Pemilik</strong></td>
                <td style="padding: 5px 0px;"><strong>:</strong></td>
                <td style="padding: 5px 0px;"><strong>{{ $produk->toko->user->full_name }}</strong></td>
              </tr>
              <tr>
                <td style="width: 200px;padding: 5px 0px;"><strong>Alamat Toko</strong></td>
                <td style="padding: 5px 0px;"><strong>:</strong></td>
                <td style="padding: 5px 0px;"><strong>{{ $produk->toko->alamat_toko }}</strong></td>
              </tr>
            </table>
            <div class="d-flex align-items-center mt-3" style="gap: 7px;">
              <a href="{{ route('admin.toko.view', $produk->toko->kode_toko) }}?by_produk={{ $produk->kode_produk }}" class="btn btn-primary btn-sm">Lihat Toko</a>
              <a href="{{ route('admin.user.view', $produk->toko->user->uuid) }}?by_produk={{ $produk->kode_produk }}" class="btn btn-success btn-sm">Lihat User</a>
            </div>
          </div>
        </div>
        <hr>
        <form action="{{ route('admin.produk.update', $produk->kode_produk) }}" method="POST">
          @csrf
          @method('put')
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Nama Produk</label>
                  <input type="text" class="form-control form-control-sm" readonly value="{{ $produk->nm_produk }}">
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Type Produk</label>
                  <input type="text" class="form-control form-control-sm" readonly value="{{ $produk->type_produk }}">
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Kategori</label>
                  <input type="text" class="form-control form-control-sm" readonly value="{{ $produk->kategori->nama_kategori }}">
                </div>
                <div class="col-md-12 mb-3">
                  <div class="row">
                    <div class="col-md-6">
                      <label for="" class="form-label">Harga</label>
                      <input type="text" class="form-control form-control-sm" readonly value="Rp. {{ number_format($produk->harga, 2) }}">
                    </div>
                    <div class="col-md-6">
                      <label for="" class="form-label">Harga Diskon</label>
                      <input type="text" class="form-control form-control-sm" readonly value="Rp. {{ $produk['detail_harga']['harga_diskon'] }}">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="w-100 border" style="min-height: 300px;">
                <img width="100%" src="{{ $produk->image }}" alt="{{ $produk->nm_produk }}">
              </div>
            </div>
            <div class="col-md-12 mb-4">
              <textarea name="deskripsi" id="summernote">{{ nl2br($produk->deskripsi) }}</textarea>
            </div>
            <div class="col-md-12 d-flex align-items-center" style="gap: 7px;">
              <a href="{{ isset($url_back) ? $url_back : route('admin.produk.view-produk', $produk) }}" class="btn btn-primary">Kembali</a>
              <button class="btn btn-danger" type="button" id="batal_konfirmasi">Batal Konfirmasi</button>
              <button class="btn btn-success" type="submit">Update Deskripsi</button>
            </div>
          </div>
        </form>
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
  });
</script>
@endsection