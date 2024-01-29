@extends('layouts.main', ['title' => 'Konfirmasi Pembayaran'])
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
    <h1 style="font-size: 1.5em">Konfirmasi Pembayaran</h1>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped w-100" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>No Order</th>
                <th>Customer</th>
                <th>Total Produk</th>
                <th>Total Biaya</th>
                <th>Total Potongan</th>
                <th>Kode Unique</th>
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
        url: '{{ route("admin.payment.konfirmasi-data") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'no_order', search: true, name: 'no_order'},
        { data: 'customer', search: true, name: 'customer'},
        { data: 'total_produk', search: true, name: 'total_produk'},
        { data: 'total_biaya', search: true, name: 'total_biaya'},
        { data: 'total_potongan', search: true, name: 'total_potongan'},
        { data: 'kode_unique', search: true, name: 'kode_unique'},
        { data: 'action', name: 'action'},
      ]
    })
  })

  function konfirmasi_payment(no_order) {
    $.post(`{{ route("admin.payment.konfirmasi") }}`, {
      _token: '{{ csrf_token() }}',
      no_order: no_order
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