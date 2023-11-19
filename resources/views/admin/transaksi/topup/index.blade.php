@extends('layouts.main', ['title' => 'Transaksi Topup'])
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
    <h1 style="font-size: 1.5em">Transaksi Topup</h1>
    <div class="card">
      <div class="card-body">
        <h5>Filter</h5>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="d-flex align-items-center justify-content-end" style="gap: 7px">
                    <div class="dropdown show">
                        <a class="btn btn-primary btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Filter Tanggal
                        </a>
                        
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <label for="" class="m-0 mr-2">Mulai</label>
                                    <input type="date" id="tanggal_mulai" value="{{ \Carbon\carbon::now()->addDay(-7)->format('Y-m-d') }}" class="form-control form-control-sm" name="" id="">
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <label for="" class="m-0 mr-2">Sampai</label>
                                    <input type="date" id="tanggal_akhir" value="{{ \Carbon\carbon::now()->format('Y-m-d') }}" class="form-control form-control-sm" name="" id="">
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="dropdown show">
                        <a class="btn btn-primary btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Type Pembayaran
                        </a>
                        
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" onclick="setTypePembayaran('TRANSFER')" href="#">
                                TRANSFER
                            </a>
                            <a class="dropdown-item" onclick="setTypePembayaran('WALLET')" href="#">
                                WALLET
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped w-100" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>No Transaksi</th>
                <th>Nama User</th>
                <th>Type Pembayaran</th>
                <th>Total Withdraw</th>
                <th>Biaya Admin</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    type_pembayaran = null;
    table = $('#table').DataTable({
      processing: true,
      serverSide: true,
      scrollCollapse: true,
      paginate: true,
      ajax: {
        method: 'POST',
        url: '{{ route("admin.transaksi.topup.data-topup") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
          d.type_pembayaran = type_pembayaran;
          console.log(type_pembayaran)
          d.tanggal_mulai = $('#tanggal_mulai').val();
          d.tanggal_akhir = $('#tanggal_akhir').val();
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'no_order', search: true, name: 'no_order'},
        { data: 'nama_user', search: true, name: 'nama_user'},
        { data: 'type_pembayaran', search: true, name: 'type_pembayaran'},
        { data: 'total_withdraw', search: true, name: 'total_withdraw'},
        { data: 'biaya_admin', search: true, name: 'biaya_admin'},
        { data: 'status_withdraw', search: true, name: 'status_withdraw'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    });

    $('#tanggal_mulai').change(function(event) {
      table.ajax.reload();
    });
    $('#tanggal_akhir').change(function(event) {
      table.ajax.reload();
    });
  })

  function setTypePembayaran(type) {
    type_pembayaran = type;
    table.ajax.reload();
    console.log(type_pembayaran)
  }
</script>
@endsection