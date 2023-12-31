@extends('layouts.main', ['title' => 'Daftar Order'])
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
    <h1 style="font-size: 1.5em">Daftar Order</h1>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>No Order</th>
                <th>Nama Produk</th>
                <th>Type Produk</th>
                <th>Kategori</th>
                <th>Nama Pembeli</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
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
        url: '{{ route("admin.order.data-order") }}',
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
        { data: 'nama_produk', search: true, name: 'nama_produk'},
        { data: 'type_produk', search: true, name: 'type_produk'},
        { data: 'kategori', search: true, name: 'kategori'},
        { data: 'nama_pembeli', search: true, name: 'nama_pembeli'},
        { data: 'biaya', search: true, name: 'biaya'},
        { data: 'status', search: true, name: 'status'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    });

    $('#no_order').keyup(function(event) {
      table.ajax.reload();
    });
    $('#type_pembayaran').change(function(event) {
      table.ajax.reload();
    });
    $('#bank').change(function(event) {
      table.ajax.reload();
    });
    $('#status_order').change(function(event) {
      table.ajax.reload();
    });
    $('#tanggal_mulai').change(function(event) {
      table.ajax.reload();
    });
    $('#tanggal_akhir').change(function(event) {
      table.ajax.reload();
    });
  })

</script>
@endsection