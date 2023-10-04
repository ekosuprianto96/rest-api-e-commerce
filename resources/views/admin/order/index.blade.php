@extends('layouts.main', ['title' => 'Daftar Order'])
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
    <h1 style="font-size: 1.5em">Daftar Order</h1>
    <div class="card">
      <div class="card-body">
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
                      <img src="{{ asset('produk/image') }}/{{ $value->toko->nama_toko }}/{{ $value->image }}" alt="{{ $value->nm_produk }}">
                    </td>
                    <td>{{ $value->kode_produk }}</td>
                    <td>{{ $value->nm_produk }}</td>
                    <td>{{ $value->type_produk }}</td>
                    <td>{{ $value->kategori->nama_kategori }}</td>
                    <td>{{ $value->harga }}</td>
                    <td>{{ $value->toko->nama_toko }}</td>
                    <td>0</td>
                    <td>
                      <span class="badge {{ $value->status_confirm == 0 ? 'badge-warning' : 'badge-succes' }} badge-sm">{{ $value->status_confirm == 0 ? 'Belum Di Konfirmasi' : 'Konfirmasi' }}</span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center" style="gap: 7px;">
                        <a href="" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> View</a>
                        <form class="m-0" action="{{ route('admin.produk.konfirmasi', $value->kode_produk) }}" method="POST">
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
              {{ $produk->links() }}
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection