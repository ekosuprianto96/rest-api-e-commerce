@extends('layouts.main', ['title' => 'Daftar Permission'])
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
        <h1 style="font-size: 1.5em">Daftar Permission</h1>
      </div>
      <div class="col-md-6">
        <div class="w-100 h-100 text-right">
          <a href="javascript:void(0)" id="tambahPermission" class="btn btn-sm btn-primary">Tambah Permission</a>
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
                <th>Nama Alias</th>
                <th>Description</th>
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

{{-- Modal --}}
<div class="modal fade" id="modalTambah" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="" id="form-create-permission" method="POST" class="w-100 m-0">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tambahRoleLabel">Tambah Permission</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="">Nama :</label>
                  <input type="text" onkeyup="checkValidate(event)" placeholder="Nama..." name="nama" id="nama" class="form-control form-control-sm">
                  <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="">Nama Alias :</label>
                  <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Alias..." name="nama_alias" id="nama_alias" class="form-control form-control-sm">
                  <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_alias"></span>
                </div>
                <div class="col-md-12 mb-3">
                  <label for="">Deskripsi :</label>
                  <textarea id="description" onkeyup="checkValidate(event)" placeholder="Deskripsi..." name="description" class="form-control form-control-sm" rows="3" style="resize: none;"></textarea>
                  <span class="text-danger error-text" style="font-size: 0.8em" data-error="description"></span>
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="batal" class="btn btn-danger" data-dismiss="modal">Batal</button>
            <button type="button" id="simpan_data" class="btn btn-primary">Simpan</button>
          </div>
      </div>
    </form>
  </div>
</div>
{{-- Modal --}}
<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="" id="form-edit-permission" method="POST" class="w-100 m-0">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEditLabel">Edit Permission</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <input type="hidden" id="permission_id" class="form-control form-control-sm">
              <div class="col-md-6 mb-3">
                <label for="">Nama :</label>
                <input type="text" onkeyup="checkValidate(event, {disabledButton: '#update_data'})" placeholder="Nama..." name="nama_edit" id="nama_edit" class="form-control form-control-sm">
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama"></span>
              </div>
              <div class="col-md-6 mb-3">
                <label for="">Nama Alias :</label>
                <input type="text" onkeyup="checkValidate(event, {disabledButton: '#update_data'})" placeholder="Nama Alias..." name="nama_alias_edit" id="nama_alias_edit" class="form-control form-control-sm">
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_alias"></span>
              </div>
              <div class="col-md-12 mb-3">
                <label for="">Deskripsi :</label>
                <textarea id="description_edit" onkeyup="checkValidate(event, {disabledButton: '#update_data'})" name="'description_edit" placeholder="Deskripsi..." class="form-control form-control-sm" rows="3" style="resize: none;"></textarea>
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="description"></span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            <button type="button" id="update_data" class="btn btn-primary">Update</button>
          </div>
      </div>
    </form>
  </div>
</div>

<script>
  $(function() {
    $('#tambahPermission').click(function(event) {
      $('#modalTambah').modal().show();
    });

    table = $('#table').DataTable({
      processing: true,
      serverSide: true,
      paginate: true,
      ajax: {
        method: 'POST',
        url: '{{ route("admin.permission.data-permission") }}',
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
        { data: 'nama_alias', search: true, name: 'nama_alias'},
        { data: 'description', search: true, name: 'description'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    });

    $('#simpan_data').click(function(event) {
      $.post('{{ route("admin.permission.store") }}', {
        nama: $('#nama').val(),
        nama_alias: $('#nama_alias').val(),
        description: $('#description').val(),
        _token: '{{ csrf_token() }}'
      }).done(function(response) {
        $.toast({
            heading: 'Sukses',
            text: response.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'success'
        });
        table.ajax.reload();
        $('#modalTambah').modal('hide');
      }).fail(function(err) {
        const error = err.responseJSON;
        $.toast({
            heading: 'Error',
            text: error.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
        $.each(error.errors, function (index, value) { 
          $(`[name=${index}]`).addClass('is-invalid');
          $(`[data-error=${index}]`).text(value);
        });
      })
    });

    $('#update_data').click(function(event) {
      $.post('{{ route("admin.permission.update") }}', {
        nama: $('#nama_edit').val(),
        nama_alias: $('#nama_alias_edit').val(),
        description: $('#description_edit').val(),
        permission_id: $('#permission_id').val(),
        _token: '{{ csrf_token() }}'
      }).done(function(response) {
        $.toast({
            heading: 'Sukses',
            text: response.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'success'
        });
        table.ajax.reload();
        $('#modalEdit').modal('hide');
      }).fail(function(err) {
        const error = err.responseJSON;
        $.toast({
            heading: 'Error',
            text: error.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
        $.each(error.errors, function (index, value) { 
          $(`[name=${index}]`).addClass('is-invalid');
          $(`[data-error=${index}]`).text(value);
        });
      })
    });

    $('#modalTambah').on('hide.bs.modal', function() {
      const errorText = $('.error-text');
      const input = $('input, textarea, select');
      $.each(errorText, function(index, value) {
        $(value).text('');
      });
      $.each(input, function(index, value) {
        $(value).removeClass('is-invalid');
      });
      $('#form-create-permission').trigger('reset');
    });

    $('#modalEdit').on('hide.bs.modal', function() {
      const errorText = $('.error-text');
      const input = $('input, textarea, select');
      $.each(errorText, function(index, value) {
        $(value).text('');
      });
      $.each(input, function(index, value) {
        $(value).removeClass('is-invalid');
      });
      $('#form-edit-permission').trigger('reset');
    });

  })

  function deletedPermission(event) {
    const permissionId = $(event.target).attr('data-permission');
    Swal.fire({
      title: "Konfirmasi",
      text: "Apakah Anda Yakin Ingin Menghapus Permission Ini?",
      icon: "warning",
      showCancelButton: true,
      cancelButtonText: 'Tidak',
      confirmButtonText: 'Ya, Hapus',
      allowOutsideClick: false,
      showLoaderOnConfirm: true
    }).then(value => {
      if(value.isConfirmed) {
        $.post('{{ route("admin.permission.destroy") }}', {
          permission_id: permissionId,
          _token: '{{ csrf_token() }}'
        }).done(function(response) {
          const {status, error, message, detail} = response;
          if(status && !error) {
            $.toast({
              heading: 'Sukses',
              text: message,
              showHideTransition: 'slide',
              position: 'top-right',
              icon: 'success'
            });
            table.ajax.reload();
          }
        }).fail(function(err) {
          const error = err.responseJSON;
            $.toast({
              heading: 'Error',
              text: error.message,
              showHideTransition: 'slide',
              position: 'top-right',
              icon: 'error'
            });
        })
      }
    });
  }

  function checkValidate(event, params = {disabledButton : '#simpan_data'}) {
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
  function showFormEdit(event) {
    const dataPermission = $(event.target).attr('data-permission');
    $.post("{{ route('admin.permission.edit') }}", {
      _token: '{{ csrf_token() }}',
      id_permission: dataPermission
    }).done(function(response) {
      const {status, detail, error, message} = response;
      if(status && !error) {
        $('#nama_edit').val(response.detail.name);
        $('#nama_alias_edit').val(response.detail.display_name);
        $('#description_edit').val(response.detail.description);
        $('#permission_id').val(response.detail.id);
        $('#modalEdit').modal().show();
      }else {
        $.toast({
            heading: 'Error',
            text: message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
      }
    }).fail(function(err) {
      $.toast({
          heading: 'Error',
          text: err.responseJSON.message,
          showHideTransition: 'slide',
          position: 'top-right',
          icon: 'error'
      });
    })
  }

</script>
@endsection