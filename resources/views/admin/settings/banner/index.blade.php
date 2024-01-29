@extends('layouts.main', ['title' => 'Setting Banner'])
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
            <h1 style="font-size: 1.5em">Setting Banner</h1>
        </div>
        <div class="col-md-6">
            <div class="w-100 d-flex justify-content-end align-items-center" style="gap: 7px;">
                <button class="btn btn-sm btn-primary" type="button" id="buttonUpload">Upload Banner</button>
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
                        <th>Image</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Upload By</th>
                        <th>Tanggal</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal --}}
  <div class="modal fade" id="modalUpload" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form action="" id="form-upload" method="POST" class="w-100 m-0">
        <input type="hidden" id="dataId">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalUploadLabel">Upload Banner</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="">Title</label>
                  <input type="text" onkeyup="checkValidate(event, {disabledButton: '#uploadData'})" placeholder="Title..." name="title" id="title" class="form-control form-control-sm">
                  <span class="text-danger error-text" style="font-size: 0.8em" data-error="title"></span>
                </div>
                <div class="col-md-6">
                    <label for="">Status</label>
                    <select class="form-control form-control-sm" name="" id="status">
                        <option value="1">Aktif</option>
                        <option value="0">Non Aktif</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <div class="border w-100 position-relative d-flex justify-content-center align-items-center flex-column" style="min-height: 200px;overflow: hidden;">
                        <input type="file" id="inputFile" accept="image/*" style="opacity: 0;position: absolute;top: 0;bottom: 0;left: 0;right: 0;">
                        <div id="notPreview" class="d-flex justify-content-center align-items-center flex-column">
                            <i class="ri-image-add-fill" style="font-size: 2em;"></i>
                            <span id="textFile"><i class="ri-upload-fill"></i> Upload Gambar Disini</span>
                        </div>
                        <div id="imagePrev" class="w-100 position-relative d-none justify-content-center align-items-center" style="height: max-content;">
                            <img id="bannerPrev" width="100%">
                            <button type="button" onclick="deletedFile(event)" class="bg-danger rounded-circle d-flex justify-content-center align-items-center btn position-absolute" style="width: 30px;height: 30px;right: 10px;top: 10px;"><i class="ri-close-line text-light"></i></button>
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
              <button type="button" id="uploadData" class="btn btn-primary">Simpan</button>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    fileImage = null;
    typeSend = 'store';
    table = $('#table').DataTable({
        processing: true,
        serverSide: true,
        scrollCollapse: true,
        paginate: true,
        ajax: {
            method: 'POST',
            url: '{{ route("admin.settings.banner.data-banner") }}',
            data: function(d) {
            d._token = '{{ csrf_token() }}';
            }
        },
        columns: [
                { data: '#', 
                    render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }},
                { data: 'image', search: true, name: 'image'},
                { data: 'title', search: true, name: 'title'},
                { data: 'status', search: true, name: 'status'},
                { data: 'uploadBy', search: true, name: 'uploadBy'},
                { data: 'tanggal', search: true, name: 'tanggal'},
                { data: 'action'}
            ]
    });

    $('#buttonUpload').click(function(event) {
        typeSend = 'store';
        $('#modalUpload').modal('show');
    })
        
    $('#inputFile').change(function(event) {
        const file = event.target.files[0];
        fileImage = file;
        const url = URL.createObjectURL(file);
        $('#notPreview').removeClass('d-flex').addClass('d-none');
        $('#imagePrev').removeClass('d-none').addClass('d-flex');
        $('#bannerPrev').prop('src', url);
    });

    $('#modalUpload').on('hidden.bs.modal', function (param) { 
        clearForm('#form-upload');
        checkValidate
    });

    $('#uploadData').click(function(event) {
        console.log(typeSend)
        sendData(typeSend);
    })

});

function sendData(type = 'store') {
    if(type == 'store') {
        const formData = new FormData();
        formData.append('title', $('#title').val());
        formData.append('status', $('#status').val());
        formData.append('image', fileImage);
        formData.append('_token', '{{ csrf_token() }}');
        $.ajax({
            url: '{{ route("admin.settings.banner.store") }}',
            method: 'POST',
            data: formData,
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
            $('#modalUpload').modal('hide');
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
    }else {
        const formData = new FormData();
        console.log(fileImage);
        formData.append('title', $('#title').val());
        formData.append('status', $('#status').val());
        formData.append('id', $('#dataId').val());
        formData.append('image', fileImage);
        formData.append('_token', '{{ csrf_token() }}');
        $.ajax({
            url: '{{ route("admin.settings.banner.update") }}',
            method: 'POST',
            data: formData,
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
            $('#modalUpload').modal('hide');
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
    }
}

function clearForm(target) {
    const errorText = $('.error-text');
    const input = $('input, textarea, select');
    $.each(errorText, function(index, value) {
        $(value).text('');
    });
    $.each(input, function(index, value) {
        $(value).removeClass('is-invalid');
    });
    $(target)[0].reset();
    deletedFile();
}

function editBanner(id) {
    typeSend = 'update';
    $.get('{{ route("admin.settings.banner.edit") }}?id='+id+'', function(response) {
        const { status, error, message } = response;
        const { image, id, title, an } = response.detail;
        $('#title').val(title);
        $('#status').val(an);
        $('#notPreview').removeClass('d-flex').addClass('d-none');
        $('#imagePrev').removeClass('d-none').addClass('d-flex');
        $('#bannerPrev').prop('src', image);
        $('#modalUpload').modal('show');
        $('#dataId').val(id);
    });
}

function deletedFile(event = null) {
    $('#inputFile').val(null);
    $('#notPreview').removeClass('d-none').addClass('d-flex');
    $('#imagePrev').removeClass('d-flex').addClass('d-none');
    $('#bannerPrev').prop('src', '');
}

function checkValidate(event, params = {disabledButton : '#uploadData'}) {
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

function deletedBanner(id) {
    $.post('{{ route("admin.settings.banner.destroy") }}', {
        _token: '{{ csrf_token() }}',
        id
    }).done(function (response) { 
        const { status, error, message } = response;
        if(status && !error) {
            $.toast({
                heading: 'Sukses',
                text: message,
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'success'
            });
        }else {
            $.toast({
                heading: 'Info',
                text: message,
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'warning'
            });
        }
        table.ajax.reload();
    }).fail(function(error) {
        $.toast({
            heading: 'Error',
            text: error.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
    })
}
</script>
@endsection