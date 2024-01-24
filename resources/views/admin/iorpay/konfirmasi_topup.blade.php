@extends('layouts.main', ['title' => 'Konfirmasi Topup'])
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
    <h1 style="font-size: 1.5em">Konfirmasi Topup</h1>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped w-100" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>No Transaksi</th>
                <th>Customer</th>
                <th>Total Permintaan</th>
                <th>Total Di Transfer</th>
                <th>Biaya Admin</th>
                <th>Kode Unique</th>
                <th>Tanggal</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    table = $('#table').DataTable({
      processing: true,
      serverSide: true,
      paginate: true,
      ajax: {
        method: 'POST',
        url: '{{ route("admin.iorpay.konfirmasi-topup-data") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'no_trx', search: true, name: 'no_trx'},
        { data: 'user', search: true, name: 'user'},
        { data: 'total_fixed', search: true, name: 'total_fixed'},
        { data: 'total_trx', search: true, name: 'total_trx'},
        { data: 'biaya_adm', search: true, name: 'biaya_adm'},
        { data: 'kode_unique', search: true, name: 'kode_unique'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    })
  })

  function konfirmasi_topup(no_trx) {
    $.post(`{{ route('admin.iorpay.konfirmasi-topup') }}`, {
      _token: '{{ csrf_token() }}',
      no_trx: no_trx
    }, function(response) {
      console.log(response)
      if(response.status && !response.error) {
        $.toast({
            heading: 'Success',
            text: response.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'success'
        });
        table.ajax.reload();
      }else {
        $.toast({
            heading: 'Error',
            text: response.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
      }
    })
  }
</script>
@endsection