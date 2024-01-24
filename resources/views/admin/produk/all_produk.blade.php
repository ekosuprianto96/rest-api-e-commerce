@extends('layouts.main', ['title' => 'Daftar Semua Produk'])
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
    <h1 style="font-size: 1.5em">Daftar Semua Produk</h1>
    <div class="card">
      <div class="card-body">
        <h5>Filter Produk</h5>
        <div class="row mb-3">
          <div class="col-md-4">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <input name="nama_produk" id="nama_produk" type="text" class="form-control form-control-sm" placeholder="Nama Produk">
          </div>
          <div class="col-md-4">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <select name="toko" id="penjual" class="form-control form-control-sm">
              <option value="">-- Pilih Penjual --</option>
              @foreach($toko as $key => $value)
              <option value="{{ $value->kode_toko }}">{{ $value->nama_toko }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <select name="kategori" id="kategori" class="form-control form-control-sm">
              <option value="">-- Pilih Kategori --</option>
              @foreach($kategori as $key => $value)
              <option value="{{ $value->kode_kategori }}">{{ $value->nama_kategori }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12 mt-2 d-flex align-items-center" style="gap: 7px;">
            <a href="{{ route('admin.produk.all-produk') }}" class="btn btn-sm btn-warning px-4">Refresh</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped w-100" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Type Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Penjual</th>
                <th>Terjual</th>
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
        url: '{{ route("admin.produk.data-produk") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
          d.nama_produk = $('#nama_produk').val();
          d.penjual = $('#penjual').val();
          d.kategori = $('#kategori').val();
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'image', search: true, name: 'image'},
        { data: 'kode_produk', search: true, name: 'kode_produk'},
        { data: 'nama_produk', search: true, name: 'nama_produk'},
        { data: 'type_produk', search: true, name: 'type_produk'},
        { data: 'kategori', search: true, name: 'kategori'},
        { data: 'harga', search: true, name: 'harga'},
        { data: 'nama_toko', search: true, name: 'nama_toko'},
        { data: 'terjual', search: true, name: 'terjual'},
        { data: 'status', search: true, name: 'status'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    });

    $('#nama_produk').keyup(function(event) {
      table.ajax.reload();
    });
    $('#penjual').change(function(event) {
      table.ajax.reload();
    });
    $('#kategori').change(function(event) {
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