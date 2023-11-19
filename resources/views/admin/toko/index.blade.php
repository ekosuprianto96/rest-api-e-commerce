@extends('layouts.main', ['title' => 'Daftar Semua Toko'])
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
    <h1 style="font-size: 1.5em">Daftar Toko</h1>
    <div class="card">
      <div class="card-body">
        <h5>Filter User</h5>
        <div class="row mb-3">
          <div class="col-md-4">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <input name="nama_toko" id="nama_toko" type="text" class="form-control form-control-sm" placeholder="Nama Toko">
          </div>
          <div class="col-md-4">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <select name="status" id="status_toko" id="" class="form-control form-control-sm">
              <option value="">-- Status --</option>
              <option value="APPROVED">APPROVED</option>
              <option value="PENDING">PENDING</option>
              <option value="REJECT">REJECT</option>
            </select>
          </div>
          <div class="col-md-12 mt-2 d-flex align-items-center" style="gap: 7px;">
            <a href="{{ route('admin.toko.index') }}" class="btn btn-sm btn-warning px-4">Refresh</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped w-100" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Nama Toko</th>
                <th>Nama Pemilik</th>
                <th>No Hape</th>
                <th>Alamat</th>
                <th>Status</th>
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
        url: '{{ route("admin.toko.data-toko") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
          d.nama_toko = $('#nama_toko').val();
          d.status_toko = $('#status_toko').val();
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'image', search: true, name: 'image'},
        { data: 'nama_toko', search: true, name: 'nama_toko'},
        { data: 'nama_pemilik', search: true, name: 'nama_pemilik'},
        { data: 'no_hape', search: true, name: 'no_hape'},
        { data: 'alamat', search: true, name: 'alamat'},
        { data: 'status', search: true, name: 'status'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    })

    $('#nama_toko').keyup(function(event) {
      table.ajax.reload();
    });
    $('#status_toko').change(function(event) {
      table.ajax.reload();
    });
  })

  function konfirmasi_toko(kode_toko) {
    $.post(`{{ route('admin.toko.konfirmasi') }}`, {
      _token: '{{ csrf_token() }}',
      kode_toko: kode_toko
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