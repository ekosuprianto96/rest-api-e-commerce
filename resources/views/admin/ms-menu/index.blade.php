@extends('layouts.main', ['title' => 'Master Menu'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
    text-align: center;
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
          <h1 style="font-size: 1.5em">Master Menu</h1>
        </div>
        <div class="col-md-6">
          <div class="w-100 h-100 text-right">
            <a href="{{ route('admin.ms-menu.create') }}" class="btn btn-sm btn-primary">Tambah Menu</a>
            <a href="javascript:void(0)" id="tambahParent" class="btn btn-sm btn-primary">Tambah Parent Menu</a>
          </div>
        </div>
      </div>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped w-100" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>ID</th>
                <th>Nama</th>
                <th>Nama Alias</th>
                <th>URL</th>
                <th>Icon</th>
                <th>Parent Menu</th>
                <th>Aktif</th>
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

{{-- Modal --}}
<div class="modal fade" id="modalParent" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalParentLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="" id="formTambahParent" method="POST" class="w-100 m-0">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalParentLabel">Tambah Parent</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="">Nama :</label>
                <input type="text" onkeyup="checkValidate(event, {disabledButton: '#simpanData'})" placeholder="Nama..." name="nama_edit" id="nama" class="form-control form-control-sm">
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama"></span>
              </div>
              <div class="col-md-6 mb-3">
                <label for="">Nama Alias :</label>
                <input type="text" onkeyup="checkValidate(event, {disabledButton: '#simpanData'})" placeholder="Nama Alias..." name="alias" id="alias" class="form-control form-control-sm">
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_alias"></span>
              </div>
              <div class="col-md-12 mb-3">
                <label for="">Order</label>
                <input type="number" class="form-control form-control-sm" id="order" value="{{ App\Models\MenuParent::max('order') + 1 }}">
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="description"></span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            <button type="button" id="simpanData" class="btn btn-primary">Simpan</button>
          </div>
      </div>
    </form>
  </div>
</div>

<script>
  $(function() {
    order = '{{ App\Models\MenuParent::max('order') + 1 }}';
    table = $('#table').DataTable({
      processing: true,
      serverSide: true,
      paginate: true,
      ajax: {
        method: 'POST',
        url: '{{ route("admin.ms-menu.data-menu") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'id', search: true, name: 'id'},
        { data: 'nama', search: true, name: 'nama'},
        { data: 'nama_alias', search: true, name: 'nama_alias'},
        { data: 'url', search: true, name: 'url'},
        { data: 'icon', search: true, name: 'icon'},
        { data: 'nama_parent', search: true, name: 'nama_parent'},
        { data: 'status', search: true, name: 'status'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    });

    $('#tambahParent').click(function(event) {
      $('#order').val(parseInt(order));
      $('#modalParent').modal('show');
    })

    $('#modalParent').on('hide.bs.modal', function() {
      const errorText = $('.error-text');
      const input = $('input, textarea, select');
      $.each(errorText, function(index, value) {
        $(value).text('');
      });
      $.each(input, function(index, value) {
        $(value).removeClass('is-invalid');
      });
      $('#formTambahParent').trigger('reset');
    })

    $('#simpanData').click(function(event) { 
      storeParent().then(response => {
        const { status, error, message, detail } = response;
        if(status && !error) {
          order = parseInt(order) + 1;
          $.toast({
            heading: 'Sukses',
            text: message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'success'
          });
          table.ajax.reload();
          $('#modalParent').modal('hide');
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
        const { message } = err.responseJSON;
        $.toast({
          heading: 'Error!',
          text: message,
          showHideTransition: 'slide',
          position: 'top-right',
          icon: 'error'
        });
      })
    })
  })


  function deletedMenu(id) {
    $.post(`{{ route("admin.ms-menu.destroy") }}`, {
      _token: '{{ csrf_token() }}',
      id: id
    }, function(response) {
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
        $.each(error.errors, function (index, value) { 
          $(`[name=${index}]`).addClass('is-invalid');
          $(`[data-error=${index}]`).text(value);
        });
      }
    })
  }

  function storeParent() {
    return new Promise((resolve, reject) => {
      $.post('{{ route("admin.ms-menu.parent.store") }}', {
        nama: $('#nama').val(),
        alias: $('#alias').val(),
        order: $('#order').val(),
        _token: '{{ csrf_token() }}'
      }).done(response => {
        resolve(response);
      }).fail(err => {
        reject(err);
      })
    })
  }

  function checkValidate(event, params = {disabledButton : '#simpanData'}) {
    const { disabledButton } = params;
    if($(event.target).val() == '') {
      $(event.target).addClass('is-invalid');
      $(event.target).next().text('Isi data dengan benar.');
      $(event.target).next().addClass('error-fedback');
    }else {
      $(event.target).removeClass('is-invalid');
      $(event.target).next().text('');
      $(event.target).next().removeClass('error-fedback');
    }

    if($('.error-fedback').length > 0) {
      $(disabledButton).prop('disabled', true);
      $(disabledButton).css({cursor: 'not-allowed'});
    }else {
      $(disabledButton).prop('disabled', false);
      $(disabledButton).css({cursor: 'pointer'});
    }
  }
</script>
@endsection