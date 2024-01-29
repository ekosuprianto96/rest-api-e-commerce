@extends('layouts.main', ['title' => 'Detail Saldo'])
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
      <div class="col-md-6 mb-3">
        <a href="{{ route('admin.detail-saldo') }}" class="btn btn-danger">Kembali</a>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">Detail Saldo</h4>
        <form action="{{ route('admin.detail-saldo') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <select name="payment" id="pilih_payment" class="form-control form-control-sm">
                        <option value="">-- Pilih Payment --</option>
                        @foreach(\App\Models\PaymentMethod::where('status_payment', 1)->get() as $key => $value)
                        <option value="{{ $value->kode_payment }}" {{ empty($data) ? '' : ($data['payment'] == $value->kode_payment ? 'selected' : '')  }}>{{ $value->payment_name }}</option>
                        @endforeach
                        <option value="gateway" {{ empty($data) ? '' : ($data['payment'] == 'gateway' ? 'selected' : '')  }}>Midtrans</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="w-100 h-100 d-flex" style="gap: 5px;">
                        <button type="submit" class="btn btn-sm btn-success">Tarik Data</button>
                        <a href="{{ route('admin.detail-saldo') }}" class="btn btn-sm btn-warning">Refresh</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Total Saldo : <strong>Rp. {{ isset($data) ? number_format($data['total'], 0) : 0 }}</strong></h4>
            </div>
            @if(isset($data) && $data['payment'] == 'gateway')
            <div class="col-md-6 text-right">
                <button id="tarik-dana" data-toggle="modal" class="btn btn-sm btn-primary">Penarikan Dana</button>
            </div>
            @endif
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>No Transaksi</th>
                                <th>Nama User</th>
                                <th>Type</th>
                                <th>Metode Pembayaran</th>
                                <th>Total Biaya</th>
                                <th>Keterangan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data && count($data['transaksi']) > 0) 
                                @foreach($data['transaksi'] as $key => $value)
                                <tr>
                                    <td>{{ $data['transaksi']->firstItem() + $key }}</td>
                                    <td>{{ $value->no_transaksi }}</td>
                                    <td>{{ $value->user->full_name }}</td>
                                    <td>{{ $value->type_payment }}</td>
                                    <td>{{ isset($value->bank) ? $value->bank->payment_name : $value->method }}</td>
                                    @if($value->jns_payment == 'DEBIT')
                                    <td class="text-right text-success">+Rp. {{ number_format($value->total, 0) }}</td>
                                    @else
                                    <td class="text-right text-danger">-Rp. {{ number_format($value->total, 0) }}</td>
                                    @endif
                                    <td>{{ $value->keterangan }}</td>
                                    <td>{{ $value->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" align="center" class="p-3">
                                        <strong>Tidak Ada Data</strong>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal --}}
  <div class="modal fade" id="penarikanDana" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="penarikanDanaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form action="{{ route('admin.tarik-dana') }}" method="POST" class="w-100 m-0">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="penarikanDanaLabel">Penarikan Dana</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <h5>Saldo : {{ isset($data) ? number_format($data['total'], 0) : 0 }}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Dari :</label>
                        <input value="Midtrans" type="text" readonly class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label for="">Ke Bank :</label>
                        <select name="payment" id="pilih_payment" class="form-control form-control-sm">
                            <option value="">-- Pilih Bank --</option>
                            @foreach(\App\Models\PaymentMethod::where('status_payment', 1)->get() as $key => $value)
                            <option value="{{ $value->kode_payment }}">{{ $value->payment_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Tarik Dana</button>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $(function() {
    $('#tarik-dana').click(function() {
        $('#penarikanDana').modal('show');
    })
  })
</script>
@endsection