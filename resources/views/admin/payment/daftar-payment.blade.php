@extends('layouts.main', ['title' => 'Daftar Payment'])
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
        <h1 style="font-size: 1.5em">Daftar Payment</h1>
      </div>
      <div class="col-md-6">
        <div class="w-100 h-100 text-right">
          <a href="javascript:void(0)" id="tambah_payment" class="btn btn-sm btn-primary">Tambah Payment</a>
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
                <th>Icon</th>
                <th>Kode</th>
                <th>Nama Bank</th>
                <th>Nama Pemilik</th>
                <th>Norek</th>
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

{{-- Modal --}}
<div class="modal fade" id="modalTambah" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="" id="form-create-role" method="POST" class="w-100 m-0">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel">Tambah Payment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mb-3 d-flex align-items-center" style="gap: 7px">
                    <button type="button" onclick="handleTab('bank')" class="btn btn-sm btn-primary">Bank</button>
                    <button type="button" onclick="handleTab('wallet')" class="btn btn-sm btn-primary">Wallet</button>
                </div>
            </div>
            <div class="row tab-form" id="bank">
                <div class="col-md-6 mb-3">
                    <label for="">Nama Bank :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Bank..." name="nama_bank" id="nama_bank" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_bank"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Pemilik :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Pemilik..." name="nama_pemilik" id="nama_pemilik" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_pemilik"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">No Rekening :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="No Rekening..." name="norek" id="norek" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="norek"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Status</label>
                    <select name="status_bank" class="form-control form-control-sm" id="status_bank">
                    <option value="">-- Status Aktif/Tidak Aktif --</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                    </select>
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="status"></span>
                </div>
            </div>
            <div class="row tab-form" id="wallet">
                <div class="col-md-6 mb-3">
                    <label for="">Nama Wallet :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Wallet..." name="nama_wallet" id="nama_wallet" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_wallet"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Pemilik :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Pemilik..." name="nama_pemilik_wallet" id="nama_pemilik_wallet" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_pemilik_wallet"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">No Telpon/ID :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="No Telpon/ID..." name="notelpon_wallet" id="notelpon_wallet" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="notelpon_wallet"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Status</label>
                    <select name="status_wallet" class="form-control form-control-sm" id="status_wallet">
                    <option value="">-- Status Aktif/Tidak Aktif --</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                    </select>
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="status"></span>
                </div>
            </div>
            <div class="row">
              <div class="col-md-12 mb-3">
                <label for="">Upload Icon:</label>
                <input type="file" onchange="checkValidate(event)" name="icon" id="icon" class="form-control-file form-control-sm">
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="icon"></span>
              </div>
              <div class="col-md-4">
                <img alt="" width="100" id="icon_prev">
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
    <form action="" id="form-edit-role" method="POST" class="w-100 m-0">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEditLabel">Edit Payment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="kode_pay">
            <div class="row tab-form" id="bank_edit">
                <div class="col-md-6 mb-3">
                    <label for="">Nama Bank :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Bank..." name="nama_bank" id="nama_bank_edit" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_bank"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Pemilik :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Pemilik..." name="nama_pemilik" id="nama_pemilik_edit" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_pemilik"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">No Rekening :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="No Rekening..." name="norek" id="norek_edit" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="norek"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Status</label>
                    <select name="status_bank_edit" class="form-control form-control-sm" id="status_bank_edit">
                    <option value="">-- Status Aktif/Tidak Aktif --</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                    </select>
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="status"></span>
                </div>
            </div>
            <div class="row tab-form" id="wallet_edit">
                <div class="col-md-6 mb-3">
                    <label for="">Nama Wallet :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Wallet..." name="nama_wallet" id="nama_wallet_edit" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_wallet"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Pemilik :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="Nama Pemilik..." name="nama_pemilik_wallet" id="nama_pemilik_wallet_edit" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama_pemilik_wallet"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">No Telpon/ID :</label>
                    <input type="text" onkeyup="checkValidate(event)" placeholder="No Telpon/ID..." name="notelpon_wallet" id="notelpon_wallet_edit" class="form-control form-control-sm">
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="notelpon_wallet"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Status</label>
                    <select name="status_wallet_edit" class="form-control form-control-sm" id="status_wallet_edit">
                    <option value="">-- Status Aktif/Tidak Aktif --</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                    </select>
                    <span class="text-danger error-text" style="font-size: 0.8em" data-error="status"></span>
                </div>
            </div>
            <div class="row">
              <div class="col-md-12 mb-3">
                <label for="">Upload Icon:</label>
                <input type="file" onchange="checkValidate(event)" name="icon_edit" id="icon_edit" class="form-control-file form-control-sm">
                <span class="text-danger error-text" style="font-size: 0.8em" data-error="icon_edit"></span>
              </div>
              <div class="col-md-4">
                <img alt="" width="100" id="icon_prev_edit">
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
    handleTab('bank');
    type_payment = 'bank';
    type_payment_edit = 'bank_edit';
    $('#tambah_payment').click(function(event) {
      $('#modalTambah').modal().show();
    });

    $('#select_permissions_add').select2({
      width: '100%',
      placeholder: 'Pilih Permission'
    });
    $('#select_permissions_edit').select2({
      width: '100%',
      placeholder: 'Pilih Permission'
    });
    table = $('#table').DataTable({
      processing: true,
      serverSide: true,
      paginate: true,
      ajax: {
        method: 'POST',
        url: '{{ route("admin.payment.data-payment") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'image', searchable: true, name: 'image'},
        { data: 'kode_pay', searchable: true, name: 'kode_pay'},
        { data: 'nama', searchable: true, name: 'nama'},
        { data: 'nama_pemilik', searchable: true, name: 'nama_pemilik'},
        { data: 'norek', searchable: true, name: 'norek'},
        { data: 'status', name: 'status'},
        { data: 'tanggal', searchable: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ]
    });

    $('#icon').change(function() {
      const file = this.files[0];
      const url = URL.createObjectURL(file);
      $('#icon_prev').prop('src', url);
    })
    $('#icon_edit').change(function() {
      const file = this.files[0];
      const url = URL.createObjectURL(file);
      $('#icon_prev_edit').prop('src', url);
    })
    $('#simpan_data').click(function(event) {
      let dataForm = new FormData();
      if(type_payment == 'bank') {
        dataForm.append('nama_bank', $('#nama_bank').val());
        dataForm.append('nama_pemilik', $('#nama_pemilik').val());
        dataForm.append('norek', $('#norek').val());
        dataForm.append('status', $('#status_bank').val());
        dataForm.append('type', 'bank');
        dataForm.append('_token', '{{ csrf_token() }}');
        dataForm.append('icon', $('#icon').prop('files')[0]);
      }else {
        dataForm.append('nama_wallet', $('#nama_wallet').val());
        dataForm.append('nama_pemilik_wallet', $('#nama_pemilik_wallet').val());
        dataForm.append('notelpon_wallet', $('#notelpon_wallet').val());
        dataForm.append('status', $('#status_wallet').val());
        dataForm.append('type', 'wallet');
        dataForm.append('_token', '{{ csrf_token() }}');
        dataForm.append('icon', $('#icon').prop('files')[0]);
      }

      $.ajax({
        url: '{{ route("admin.payment.store") }}',
        method: 'POST',
        data: dataForm,
        // dataType: 'aplication/json',  // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,                        
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
      });
    });

    $('#update_data').click(function(event) {
      const dataForm = new FormData();
      if(type_payment_edit == 'bank_edit') {
        dataForm.append('nama_bank', $('#nama_bank_edit').val());
        dataForm.append('nama_pemilik', $('#nama_pemilik_edit').val());
        dataForm.append('norek', $('#norek_edit').val());
        dataForm.append('status', $('#status_bank_edit').val());
        dataForm.append('type', 'bank');
        dataForm.append('_token', '{{ csrf_token() }}');
        dataForm.append('kode_payment', $('#kode_pay').val());
        dataForm.append('icon', $('#icon_edit').prop('files')[0]);
      }else {
        dataForm.append('nama_wallet', $('#nama_wallet_edit').val());
        dataForm.append('nama_pemilik_wallet', $('#nama_pemilik_wallet_edit').val());
        dataForm.append('notelpon_wallet', $('#notelpon_wallet_edit').val());
        dataForm.append('status', $('#status_wallet_edit').val());
        dataForm.append('type', 'wallet');
        dataForm.append('_token', '{{ csrf_token() }}');
        dataForm.append('kode_payment', $('#kode_pay').val());
        dataForm.append('icon', $('#icon_edit').prop('files')[0]);
      }

      $.ajax({
        url: '{{ route("admin.payment.update") }}',
        method: 'POST',
        data: dataForm,
        // dataType: 'aplication/json',  // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,                        
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
      });
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
      $('#form-create-role').trigger('reset');
      handleTab('bank');
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
      $('#form-edit-role').trigger('reset');
      $('#select_permissions_edit').val(null).trigger('change');
    });

  })

  function handleTab(type = 'bank') {
    type_payment = type;
    const tabForm = $('.tab-form');
    $.each(tabForm, function(index, value) {
        $(value).hide();
    })
    $(`#${type}`).show();
  }
  function handleTabEdit(type = 'bank_edit') {
    type_payment_edit = type;
    const tabForm = $('.tab-form');
    $.each(tabForm, function(index, value) {
        $(value).hide();
    })
    $(`#${type}`).show();
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
  function deletedPayment(event) {
    const dataPayment = $(event.target).attr('data-payment');
    Swal.fire({
      title: "Konfirmasi",
      text: "Apakah Anda Yakin Ingin Menghapus Payment Ini?",
      icon: "warning",
      showCancelButton: true,
      cancelButtonText: 'Tidak',
      confirmButtonText: 'Ya, Hapus',
      allowOutsideClick: false,
      showLoaderOnConfirm: true
    }).then(value => {
      if(value.isConfirmed) {
        $.post('{{ route("admin.payment.destroy") }}', {
          kode_payment: dataPayment,
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
  function showFormEdit(event) {
    const kode_pay = $(event.target).attr('data-payment');
    $.post("{{ route('admin.payment.edit') }}", {
      _token: '{{ csrf_token() }}',
      kode_payment: kode_pay
    }).done(function(response) {
      const {status, detail, error, message} = response;
      if(status && !error) {
        if(detail.type == 'bank') {
          $('#nama_bank_edit').val(detail.payment_name);
          $('#nama_pemilik_edit').val(detail.nama_pemilik);
          $('#norek_edit').val(detail.no_rek);
          $('#status_bank_edit').val(detail.status_payment);
          $('#kode_pay').val(detail.kode_payment);
        }else {
          $('#nama_wallet_edit').val(detail.payment_name);
          $('#nama_pemilik_wallet_edit').val(detail.nama_pemilik);
          $('#notelpon_wallet_edit').val(detail.no_rek);
          $('#status_wallet_edit').val(detail.status_payment);
          $('#kode_pay').val(detail.kode_payment);
        }
        $('#icon_prev_edit').prop('src', detail.image);
        handleTabEdit(detail.type+'_edit');
        type_payment_edit = detail.type+'_edit';
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
    });
  }

</script>
@endsection