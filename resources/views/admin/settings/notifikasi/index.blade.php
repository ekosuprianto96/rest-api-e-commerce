@extends('layouts.main', ['title' => 'Notifikasi Pengguna'])
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
        <div class="col-md-6">
            <h1 style="font-size: 1.5em">Notifikasi Pengguna</h1>
        </div>
        {{-- <div class="col-md-6">
            <div class="w-100 d-flex justify-content-end align-items-center" style="gap: 7px;">
                <button class="btn btn-sm btn-primary" type="button" id="buttonUpload">Upload Banner</button>
            </div>
        </div> --}}
    </div>
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-md-6 justify-content-center align-items-center" style="display: flex;height: 400px;" id="skeletonPopup">
                <div class="d-flex align-items-center"><span class="spinner-grow spinner-grow-sm mr-3" role="status" aria-hidden="true"></span> Loading...</div>
            </div>
            <div class="col-md-6" id="notifikasiPopup" style="display: none;">
                
            </div>
            <div class="col-md-6 justify-content-center align-items-center" style="display: flex;height: 400px;" id="skeletonBartop">
                <div class="d-flex align-items-center"><span class="spinner-grow spinner-grow-sm mr-3" role="status" aria-hidden="true"></span> Loading...</div>
            </div>
            <div class="col-md-6" id="notifikasiBartop" style="display: none;">
                
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function() {
        renderViewPopup();
        renderViewBartop();
    });

    function renderViewPopup() {
        getViewPopup().then(response => {
            $('#skeletonPopup').hide();
            $('#notifikasiPopup').show();
            $('#notifikasiPopup').html(response);
        }).catch(err => {
            $.toast({
                heading: 'Error',
                text: err.message,
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'error'
            });
        })
    }

    function getViewPopup() {
        return new Promise((resolve, reject) => {
            $('#skeletonPopup').show();
            $('#notifikasiPopup').hide();
            $.get('{{ route("admin.notifikasi.render-popup") }}')
            .done(function(response) {
                resolve(response);
            }).fail(err => {
                reject(err);
            })
        });
    }
    function renderViewBartop() {
        getViewBartop().then(response => {
            $('#skeletonBartop').hide();
            $('#notifikasiBartop').show();
            $('#notifikasiBartop').html(response);
        }).catch(err => {
            $.toast({
                heading: 'Error',
                text: err.message,
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'error'
            });
        });
    }

    function getViewBartop() {
        return new Promise((resolve, reject) => {
            $('#skeletonBartop').show();
            $('#notifikasiBartop').hide();
            $.get('{{ route("admin.notifikasi.render-bartop") }}')
            .done(function(response) {
                resolve(response);
            }).fail(err => {
                reject(err);
            })
        });
    }

</script>
@endsection