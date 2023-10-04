@extends('layouts.main', ['title' => 'Daftar Toko Belum Dikonfirmasi'])
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
    <h1 style="font-size: 1.5em">Daftar Toko</h1>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Nama Toko</th>
                <th>Nama Pemilik</th>
                <th>No Hape</th>
                <th>Alamat</th>
                <th>Status</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if($toko->count() > 0) 
                @foreach($toko as $key => $value)
                  <tr>
                    <td>
                      {{ $toko->firstItem() + $key }}
                    </td>
                    <td>
                      <img src="{{ asset('toko/image') }}/{{ $value->nama_toko }}/{{ $value->image }}" alt="{{ $value->nama_toko }}">
                    </td>
                    <td>{{ $value->nama_toko }}</td>
                    <td>{{ $value->user->full_name }}</td>
                    <td>{{ $value->user->no_hape }}</td>
                    <td>{{ $value->alamat_toko }}</td>
                    <td>
                      <span class="badge {{ $value->status_toko == 'APPROVED' ? 'badge-success' : ($value->status_toko == 'PENDING' ? 'badge-warning' : 'badge-danger') }} badge-sm">{{ $value->status_toko }}</span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center" style="gap: 7px;">
                        <a href="{{ route('admin.toko.view', $value->kode_toko) }}" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                        <form class="m-0" action="{{ route('admin.toko.konfirmasi', $value->kode_toko) }}" method="POST">
                          @method('PUT')
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="ri-checkbox-circle-fill"></i> Konfirmasi</button>
                        </form>
                        <form class="m-0" action="{{ route('admin.toko.reject', $value->kode_toko) }}" method="POST">
                          @method('PUT')
                          @csrf
                          <button type="submit" class="btn btn-sm btn-danger text-nowrap" style="font-size: 0.8em"><i class="ri-delete-bin-5-fill"></i> Reject</button>
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
              {{ $toko->links() }}
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection