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
            <div class="row">
              <div class="col-md-12 mb-3 border ">
                <div class="w-100 position-relative d-flex justify-content-center align-items-center" style="height: 300px;">
                  @if(!$user->image)
                    <i class="ri-file-user-fill" style="font-size: 5em;"></i>
                  @else
                    <img width="100%" src="{{ $user->image }}" alt="">
                  @endif
                  <span class="badge badge-sm badge-danger" id="badge-status-nonaktif" style="position: absolute;top: 10px;right: 10px;display: {{ $user->status_banned == 1 ? 'inline-block' : 'none' }}">Akun Diblockir</span>
                  <span class="badge badge-sm badge-success" id="badge-status-aktif" style="position: absolute;top: 10px;right: 10px;display: {{ $user->status_banned == 0 ? 'inline-block' : 'none' }}">Akun Aktif</span>
                </div>
              </div>
              <div class="col-md-12 px-0 d-flex align-items-center" style="gap: 7px;" id="wrapperButton">
                <a href="{{ isset($url_back) ? $url_back : route('admin.user.index') }}" class="btn btn-primary btn-sm">Kembali</a>
                <button data-uuid-pengguna="{{ $user->uuid }}" {{ $user->status_banned == 1 ? 'disabled' : '' }} style="cursor: {{ $user->status_banned == 1 ? 'not-allowed' : 'pointer' }}" class="btn btn-danger btn-sm" type="button" id="blockPengguna">Block Pengguna</button>
                <button data-uuid-pengguna="{{ $user->uuid }}" {{ $user->status_banned == 0 ? 'disabled' : '' }} style="cursor: {{ $user->status_banned == 0 ? 'not-allowed' : 'pointer' }}" type="button" id="bukaBlock" class="btn btn-sm btn-warning">Buka Block</button>
              </div>
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
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    $('#blockPengguna').click(function(event) {
      const uuid = $(this).attr('data-uuid-pengguna');
      blockPengguna(uuid).then(result => {
        const { status, error, message, detail } = result;
        if(status && !error) {
          $(this).prop('disabled', true);
          $(this).css({'cursor': 'not-allowed'});
          $('#bukaBlock').removeAttr('disabled');
          $('#bukaBlock').css({'cursor': 'pointer'});
          renderBadgeStatusPengguna(true);
          $.toast({
              heading: 'Success',
              text: message,
              showHideTransition: 'slide',
              position: 'top-right',
              icon: 'success'
          });
        }else {
          $.toast({
              heading: 'Gagal!',
              text: message,
              showHideTransition: 'slide',
              position: 'top-right',
              icon: 'warning'
          });
        }
      }).catch(err => {
        const { status, error, message, detail } = err.responseJSON;
        $.toast({
            heading: 'Error',
            text: message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
      })
    });

    $('#bukaBlock').click(function(event) {
      const uuid = $(this).attr('data-uuid-pengguna');
      bukaBlock(uuid).then(result => {
        const { status, error, message, detail } = result;
        if(status && !error) {
          $(this).prop('disabled', true);
          $('#blockPengguna').removeAttr('disabled');
          $('#blockPengguna').css({'cursor': 'pointer'});
          $(this).css({'cursor': 'not-allowed'});
          renderBadgeStatusPengguna(false);
          $.toast({
              heading: 'Success',
              text: message,
              showHideTransition: 'slide',
              position: 'top-right',
              icon: 'success'
          });
        }else {
          $.toast({
              heading: 'Gagal!',
              text: message,
              showHideTransition: 'slide',
              position: 'top-right',
              icon: 'warning'
          });
        }
      }).catch(err => {
        const { status, error, message, detail } = err.responseJSON;
        $.toast({
            heading: 'Error',
            text: message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
      })
    });
  });

  function renderBadgeStatusPengguna(status = false) {
    if(status) {
      $('#badge-status-aktif').css({'display': 'none'});
      $('#badge-status-nonaktif').css({'display': 'inline-block'});
    }else {
      $('#badge-status-aktif').css({'display': 'inline-block'});
      $('#badge-status-nonaktif').css({'display': 'none'});
    }
  }

  function blockPengguna(uuid) {
    return new Promise((resolve, reject) => {
      $.post('{{ route("admin.user.block-pengguna") }}', {
        uuid,
        _token: '{{ csrf_token() }}'
      }).done(function(response) {
        resolve(response);
      }).fail(err => {
        reject(err);
      })
    })
  }
  function bukaBlock(uuid) {
    return new Promise((resolve, reject) => {
      $.post('{{ route("admin.user.buka-block") }}', {
        uuid,
        _token: '{{ csrf_token() }}'
      }).done(function(response) {
        resolve(response);
      }).fail(err => {
        reject(err);
      })
    })
  }
</script>
@endsection