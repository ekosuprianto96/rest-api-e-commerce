@extends('layouts.main', ['title' => 'Detail Akun User'])
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
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="border w-100" style="height: 300px;">

            </div>
          </div>
          <div class="col-md-8">
            <div class="row">
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Nama User</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $user->full_name }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Username</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $user->username }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Email</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $user->email }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">No Hape</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $user->no_hape }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Alamat</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $user->alamat }}">
              </div>
            </div>
            <div class="col-md-12 d-flex align-items-center" style="gap: 7px;">
              <a href="{{ route('admin.user.index') }}" class="btn btn-primary">Kembali</a>
              @if($user->status_user == 1)
                <form class="m-0" action="{{ route('admin.user.batal-konfirmasi', $user->uuid) }}" method="POST">
                  @method('PUT')
                  @csrf
                  <button class="btn btn-danger" type="submit" id="batal_konfirmasi">Batal Konfirmasi</button>
                </form>
              @elseif($user->status_user == 0)
                <form class="m-0" action="{{ route('admin.user.konfirmasi', $user->uuid) }}" method="POST">
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
  </div>
</div>
@endsection