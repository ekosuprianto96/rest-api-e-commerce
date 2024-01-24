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
                        <a href="javascript:void(0)" onclick="viewDetailWithdraw('{{ $value->no_trx }}')" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
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

<div class="modal fade" id="modalDetailWithdraw" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalDetailWithdrawLabel" aria-hidden="true">
  <div class="modal-dialog modl-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailWithdrawLabel">Detail Permintaan Withdraw</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <ul class="w-100 mb-3 m-0 p-0">
              <li class="py-2 px-2 d-flex justify-content-between align-items-center">
                <span>No Transaksi</span>
                <span id="noTrx">-</span>
              </li>
              <li class="py-2 px-2 d-flex justify-content-between align-items-center">
                <span>Nama Customer</span>
                <span id="namaUser">-</span>
              </li>
              <li class="py-2 px-2 d-flex justify-content-between align-items-center">
                <span>Total Permintaan</span>
                <span id="totalPermintaan">0</span>
              </li>
              <li style="border-bottom: 2px dotted rgb(165, 165, 165);" class="py-2 px-2 mb-2 d-flex justify-content-between align-items-center">
                <span>Biaya Admin</span>
                <span id="biayaAdmin">0</span>
              </li>
              <li class="py-2 px-2 bg-danger text-light d-flex justify-content-between align-items-center">
                <span>Total Diterima</span>
                <span id="totalDiterima">0</span>
              </li>
            </ul>
            <h5 class="py-3" style="border-bottom: 2px dotted rgb(165, 165, 165);">Transfer Ke:</h5>
            <ul class="w-100 m-0 p-0">
              <li class="py-2 px-2 d-flex justify-content-between align-items-center">
                <span>Nama Pemilik</span>
                <span id="namaPemilik">-</span>
              </li>
              <li class="py-2 px-2 d-flex justify-content-between align-items-center">
                <span>Nama Bank/Wallet</span>
                <span id="namaAccount">-</span>
              </li>
              <li class="py-2 px-2 d-flex justify-content-between align-items-center">
                <span>No Rek/No Wallet</span>
                <span id="norek">0</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
  });


  function viewDetailWithdraw(notrx) {
    getDetailTrx(notrx).then(response => {
      const { status, error, message, detail } = response;
      if(status && !error) {
        $('#noTrx').text(detail.no_trx);
        $('#namaUser').text(detail.nama_user);
        $('#biayaAdmin').text('Rp. '+detail.biaya_admin);
        $('#totalPermintaan').text('Rp. '+detail.total_withdraw);
        $('#totalDiterima').text('Rp. '+detail.total_withdraw);
        $('#namaPemilik').text(detail.nama_pemilik);
        $('#namaAccount').text((detail.type_pembayaran == 'wallet' ? detail.nama_wallet : detail.bank.payment_name));
        $('#norek').text((detail.type_pembayaran == 'wallet' ? detail.nomor_wallet : detail.norek_tujuan));
        $('#modalDetailWithdraw').modal('show');
      }else {
        $.toast({
            heading: 'Error',
            text: response.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
      }
    }).catch(err => {
      const { message } = err;
      $.toast({
          heading: 'Error',
          text: message,
          showHideTransition: 'slide',
          position: 'top-right',
          icon: 'error'
      });
    });
  }

  function getDetailTrx(notrx) {
    return new Promise((resolve, reject) => {
      $.get('{{ url("/admin/transaksi/withdraw/detail-withdraw") }}?notrx='+notrx)
        .done(response => {
          resolve(response);
        }).fail(err => {
          reject(err);
        })
    })
  }
</script>
@endsection