@extends('layouts.main', ['title' => 'Daftar Semua Produk'])
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
    <h1 style="font-size: 1.5em">Daftar Semua Produk</h1>
    <div class="card">
      <div class="card-body">
        <form action="">
          <h5>Filter Produk</h5>
          <div class="row">
            <div class="col-md-4">
              {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
              <input name="nama_produk" type="text" class="form-control form-control-sm" placeholder="Nama Produk">
            </div>
            <div class="col-md-4">
              {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
              <select name="toko" id="" class="form-control form-control-sm">
                <option value="">-- Pilih Penjual --</option>
                @foreach($toko as $key => $value)
                <option value="{{ $value->kode_toko }}">{{ $value->nama_toko }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
              <select name="kategori" id="" class="form-control form-control-sm">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategori as $key => $value)
                <option value="{{ $value->kode_kategori }}">{{ $value->nama_kategori }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-12 mt-2 d-flex align-items-center" style="gap: 7px;">
              <button class="btn btn-sm btn-primary px-4">Cari</button>
              <a href="{{ route('admin.produk.all-produk') }}" class="btn btn-sm btn-warning px-4">Refresh</a>
            </div>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Type Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Penjual</th>
                <th>Terjual</th>
                <th>Status</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if($produk->count() > 0) 
                @foreach($produk as $key => $value)
                  <tr>
                    <td>
                      {{ $produk->firstItem() + $key }}
                    </td>
                    <td>
                      <img width="80" src="{{ $value->image }}" alt="{{ $value->nm_produk }}">
                    </td>
                    <td>{{ $value->kode_produk }}</td>
                    <td>{{ $value->nm_produk }}</td>
                    <td>{{ $value->type_produk }}</td>
                    <td>{{ $value->kategori->nama_kategori }}</td>
                    <td>{{ number_format($value->harga, 2) }}</td>
                    <td>{{ $value->toko->nama_toko }}</td>
                    <td>0</td>
                    <td>
                      <span class="badge {{ $value->status_confirm == 0 ? 'badge-warning' : 'badge-success' }} badge-sm">{{ $value->status_confirm == 0 ? 'Belum Di Konfirmasi' : 'Konfirmasi' }}</span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center" style="gap: 7px;">
                        <a href="{{ route('admin.produk.view-produk', $value->kode_produk) }}" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> View</a>
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
              {{ $produk->links() }}
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection