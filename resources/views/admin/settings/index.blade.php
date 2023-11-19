@extends('layouts.main', ['title' => 'Setting Website'])
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
    <h1 style="font-size: 1.5em"></h1>
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <h4>Settings Website</h4>
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Nama Aplikasi</label>
                  <input type="text" name="app_name" class="form-control form-control-sm" value="{{ $settings_web->app_name }}">
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Lama Clearing Saldo <span class="text-danger" style="font-size: 0.8em">(Perhitungan Menggunakan Hari)</span></label>
                  <input type="number" name="lama_clearing_saldo" class="form-control form-control-sm" value="{{ $settings_web->lama_clearing_saldo }}">
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Biaya Platform <span class="text-danger" style="font-size: 0.8em">(Menggunakan Perhitungan Persen)</span></label>
                  <input type="number" name="biaya_platform" class="form-control form-control-sm" value="{{ floatVal($settings_web->biaya_platform) }}">
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Biaya Admin <span class="text-danger" style="font-size: 0.8em">(Biaya Admin Diterapkan Di Setiap Transaksi Iorpay)</span></label>
                  <input type="number" name="biaya_admin" class="form-control form-control-sm" value="{{ floatVal($settings_web->biaya_platform) }}">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <h4>Settings Gateway</h4>
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Server Key</label>
                  <input type="text" name="server_key" class="form-control form-control-sm" value="{{ $settings_gateway->server_key }}">
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Client Key</label>
                  <input type="text" name="client_key" class="form-control form-control-sm" value="{{ $settings_gateway->client_key }}">
                </div>
                <div class="col-md-12 mb-3">
                  <label for="" class="form-label">Satatus Gateway</label>
                  <select name="status_gateway" name="status_gateway" class="form-control form-control-sm">
                    <option {{ $settings_gateway->status_gateway == 1 ? 'selected' : '' }} value="1">Aktif</option>
                    <option {{ $settings_gateway->status_gateway == 0 ? 'selected' : '' }} value="0">Tidak Aktif</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-12 d-flex align-items-center" style="gap: 7px;">
              <button class="btn btn-success">Update</button>
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