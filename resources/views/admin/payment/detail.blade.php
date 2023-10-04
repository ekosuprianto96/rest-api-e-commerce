@extends('layouts.main', ['title' => 'Detail Order Via Manual'])
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
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Nomor Order</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $order->no_order }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Nama Customer</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $order->user->full_name}}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Quantity</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $order->quantity }}">
              </div>
              <div class="col-md-12 mb-3">
                <div class="row">
                  <div class="col-md-6">
                    <label for="" class="form-label">Total Di Transfer</label>
                    <input type="text" class="form-control form-control-sm" readonly value="Rp. {{ number_format($order->total_biaya, 2) }}">
                  </div>
                  <div class="col-md-6">
                    <label for="" class="form-label">Kode Unique</label>
                    <input type="text" class="form-control form-control-sm" readonly value="{{ $order->kode_unique }}">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Total Pembayaran</label>
                <input type="text" class="form-control form-control-sm" readonly value="Rp. {{ number_format($order->total_biaya, 2) }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Total Potongan</label>
                <input type="text" class="form-control form-control-sm" readonly value="Rp. {{ number_format($order->total_potongan, 2) }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">Payment Method</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $order->payment->payment_name }}">
              </div>
              <div class="col-md-12 mb-3">
                <label for="" class="form-label">No Rekening</label>
                <input type="text" class="form-control form-control-sm" readonly value="{{ $order->payment->no_rek }}">
              </div>
            </div>
          </div>
          <div class="col-md-12 d-flex align-items-center" style="gap: 7px;">
            <a href="{{ route('admin.payment.index') }}" class="btn btn-primary">Kembali</a>
            <form class="m-0" action="{{ route('admin.payment.konfirmasi', $order->no_order) }}" method="POST">
              @csrf
              @method('PUT')
              <button {{ $order->status_order == 'SUCCESS' ? 'disabled' : '' }} type="submit" class="btn btn-success">Konfirmasi</button>
            </form>
          </div>
        </div>
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