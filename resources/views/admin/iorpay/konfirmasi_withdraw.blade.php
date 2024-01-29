@extends('layouts.main', ['title' => 'Konfirmasi Withdraw'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
    text-align: center;
  }
  tbody tr td {
    font-size: 0.8em;
    text-align: center;
  }
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <h1 style="font-size: 1.5em">Konfirmasi Withdraw</h1>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>No Transaksi</th>
                <th>Customer</th>
                <th>Total Permintaan</th>
                <th>Biaya Admin</th>
                <th>Tanggal</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if($trx_withdraw->count() > 0) 
                @foreach($trx_withdraw as $key => $value)
                  <tr>
                    <td>
                      {{ $trx_withdraw->firstItem() + $key }}
                    </td>
                    <td>{{ $value->no_trx }}</td>
                    <td>{{ $value->iorpay->user->full_name }}</td>
                    <td>Rp. {{ number_format($value->total_withdraw, 2) }}</td>
                    <td>Rp. {{ number_format($value->biaya_admin, 2) }}</td>
                    <td>{{ $value->created_at->format('d/m/Y') }}</td>
                    <td>
                      <div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                        <a href="{{ route('admin.payment.detail', $value->no_trx) }}" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                        <button type="button" class="btn konfirmasi btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="ri-checkbox-circle-fill"></i> Konfirmasi</button>
                      </div>
                      {{-- Modal --}}
                      <div class="modal fade" id="pilihPayment" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="pilihPaymentLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <form action="{{ route('admin.iorpay.konfirmasi-withdraw', $value->no_trx) }}" method="POST" class="w-100 m-0">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="pilihPaymentLabel">Pilih Metode Pembayaran</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label for="">Pilih Metode Pembayaran</label>
                                    <select name="account" id="pilih_payment" class="form-control form-control-sm">
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
                                <button type="submit" class="btn btn-primary">Konfirmasi</button>
                              </div>
                            </div>
                          </form>
                        </div>
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
              {{ $trx_withdraw->links() }}
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    const btnKonfirmasi = $('.konfirmasi');
    $.each(btnKonfirmasi, function (index, value) { 
       $(value).click(function() {
        $('#pilihPayment').modal('show');
       })
    });
  })
</script>
@endsection