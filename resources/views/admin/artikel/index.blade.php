@extends('layouts.main', ['title' => 'Daftar Artikel'])
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
      <div class="col-md-6">
        <h1 style="font-size: 1.5em">Daftar Artikel</h1>
      </div>
      <div class="col-md-6">
        <div class="w-100 h-100 text-right">
          <a href="{{ route('admin.artikel.create') }}" id="tambahArtikel" class="btn btn-sm btn-primary">Tambah Artikel</a>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Title</th>
                <th>Kategori</th>
                <th>Created By</th>
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
        method: 'POST',
        url: '{{ route("admin.artikel.data-artikel") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'nama', search: true, name: 'nama'},
        { data: 'title', search: true, name: 'title'},
        { data: 'kategori', search: true, name: 'kategori'},
        { data: 'created_by', search: true, name: 'created_by'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    });

  });

  function deletedArtikel(slug) {
    postDestroy(slug).then(response => {
      const { status, error, message } = response;
      if(status && !error) {
        $.toast({
            heading: 'Sukses',
            text: message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'success'
        });
        table.ajax.reload(null, false);
      }else {
        $.toast({
            heading: 'Gagal!',
            text: message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'warning'
        });
      }
    }).catch(err => {
      console.log(err);
      const {message} = err.responseJSON;
      $.toast({
          heading: 'Error!',
          text: message,
          showHideTransition: 'slide',
          position: 'top-right',
          icon: 'error'
      });
    })
  }

  function postDestroy(slug) {
    return new Promise((resolve, reject) => {
      $.post('{{ route("admin.artikel.destroy") }}', {
        _token: '{{ csrf_token() }}',
        slug: slug
      }).done(response => {
        resolve(response);
      }).fail(err => {
        reject(err);
      })
    }) 
  }
</script>
@endsection