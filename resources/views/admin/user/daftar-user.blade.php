@extends('layouts.main', ['title' => 'Daftar Semua User'])
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
    <h1 style="font-size: 1.5em">Daftar User</h1>
    <div class="card">
      <div class="card-body">
        <form action="">
          <h5>Filter User</h5>
          <div class="row">
            <div class="col-md-4">
              {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
              <input name="nama_user" type="text" class="form-control form-control-sm" placeholder="Nama User">
            </div>
            <div class="col-md-4">
              {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
              <select name="status" id="" class="form-control form-control-sm">
                <option value="">-- Status --</option>
                <option value="0">Belum Dikonfirmasi</option>
                <option value="1">Sudah Dikonfirmasi</option>
                <option value="2">Blacklist</option>
              </select>
            </div>
            <div class="col-md-12 mt-2 d-flex align-items-center" style="gap: 7px;">
              <button class="btn btn-sm btn-primary px-4">Cari</button>
              <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-warning px-4">Refresh</a>
            </div>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>No Hape</th>
                <th>Alamat</th>
                <th>Status</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if($users->count() > 0) 
                @foreach($users as $key => $value)
                  <tr>
                    <td>
                      {{ $users->firstItem() + $key }}
                    </td>
                    <td>
                      <img src="{{ asset('users/image') }}/{{ $value->uuid }}/{{ $value->image }}" alt="{{ $value->nama_users }}">
                    </td>
                    <td>{{ $value->full_name }}</td>
                    <td>{{ $value->username }}</td>
                    <td>{{ $value->email }}</td>
                    <td>{{ $value->no_hape }}</td>
                    <td>{{ $value->alamat }}</td>
                    <td>
                      @if($value->status_user == 0) 
                        <span class="badge badge-warning badge-sm">Belum Dikonfirmasi</span>
                      @elseif($value->status_user == 1)
                        <span class="badge badge-success badge-sm">Sudah Dikonfirmasi</span>
                      @else
                        <span class="badge badge-danger badge-sm">User Blacklist</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex align-items-center" style="gap: 7px;">
                        <a href="{{ route('admin.user.view', $value->uuid) }}" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
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
              {{ $users->links() }}
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection