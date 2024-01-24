@extends('layouts.main', ['title' => 'Daftar Artikel'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
  }
  tbody tr td {
    font-size: 0.8em;
  }
  div.note-editor {
    background-color: white;
  }
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1 style="font-size: 1.5em">Tambah Artikel</h1>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.artikel.store') }}" method="POST" class="w-100">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="slug">Nama</label>
                    <input value="{{ old('nama_display') }}" type="text" class="form-control @error('nama_display') is-invalid @enderror form-control-sm" id="nama_display" name="nama_display">
                    @error('nama_display')
                        <div class="invalid-fedback">
                            <span class="text-danger">{{ $message }}</span> 
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="title">Title</label>
                    <input value="{{ old('title') }}" onkeyup="generateSlug(event)" type="text" class="form-control @error('title') is-invalid @enderror form-control-sm" id="title" name="title">
                    @error('title')
                        <div class="invalid-fedback">
                            <span class="text-danger">{{ $message }}</span> 
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="slug">Slug</label>
                    <input readonly type="text" class="form-control @error('slug') is-invalid @enderror form-control-sm" id="slug" name="slug">
                    @error('slug')
                        <div class="invalid-fedback">
                            <span class="text-danger">{{ $message }}</span> 
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <div class="row h-100 w-full align-items-end">
                        <div class="col-md-8" id="wrapperKategori">
                            <select class="form-control @error('kategori') is-invalid @enderror form-control-sm" name="kategori" id="kategori">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <div class="invalid-fedback">
                                    <span class="text-danger">{{ $message }}</span> 
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <button type="button" title="Tambah Kategori" class="btn btn-sm btn-primary" id="tambahKategori">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <textarea class="@error('body') is-invalid @enderror" name="body" id="body"></textarea>
                    @error('body')
                        <div class="invalid-fedback">
                            <span class="text-danger">{{ $message }}</span> 
                        </div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <div class="w-100 d-flex justify-content-end align-items-center" style="gap: 10px;">
                        <a href="{{ route('admin.artikel.index') }}" class="btn btn-danger">Kembali</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="modalTambahKategori" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalTambahKategoriLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form action="" id="formTambahKategori" method="POST" class="w-100 m-0">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTambahKategoriLabel">Tambah Parent</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="">Nama :</label>
                  <input type="text" onkeyup="checkValidate(event, {disabledButton: '#simpanData'})" placeholder="Nama..." name="nama" id="namaKategori" class="form-control form-control-sm">
                  <span class="text-danger error-text" style="font-size: 0.8em" data-error="nama"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="">Nama Alias :</label>
                  <input type="text" onkeyup="checkValidate(event, {disabledButton: '#simpanData'})" placeholder="Nama Alias..." name="alias" id="alias" class="form-control form-control-sm">
                  <span class="text-danger error-text" style="font-size: 0.8em" data-error="alias"></span>
                </div>
                <div class="col-md-12 mb-3">
                  <label for="">Order</label>
                  <input type="number" class="form-control form-control-sm" id="order" value="{{ App\Models\KategoriArtikel::max('order') + 1 }}">
                  <span class="text-danger error-text" style="font-size: 0.8em" data-error="order"></span>
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
  $('#body').summernote({
    heigt: 400,
    minHeight: 400,
    maxHeight: 400
  });

  $(function() {
    generateSlug();
    order = '{{ App\Models\KategoriArtikel::max('order') + 1 }}';
    $('#tambahKategori').click(function(event) {
        $('#order').val(parseInt(order));
        $('#modalTambahKategori').modal('show');
    });
    $('#simpanData').click(function(event) {
        storeKategori().then(response => {
            const { status, error, message, detail } = response;
            if(status && !error) {
                order = parseInt(order) + 1;
                getViewKategori().then(result => {
                    $('#wrapperKategori').html(result);
                }).catch(function(err) {
                    const { message } = err.responseJSON;
                    $.toast({
                        heading: 'Error!',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                });
                $.toast({
                    heading: 'Sukses',
                    text: message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'success'
                });
                $('#modalTambahKategori').modal('hide');
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
            const { message, errors } = err.responseJSON;
            $.toast({
                heading: 'Error!',
                text: message,
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'error'
            });
            $.each(errors, function (index, value) { 
                $(`[name=${index}]`).addClass('is-invalid');
                $(`[data-error=${index}]`).text(value);
            });
        })
    });

    $('#modalTambahKategori').on('hide.bs.modal', function() {
      const errorText = $('.error-text');
      const input = $('input, textarea, select');
      $.each(errorText, function(index, value) {
        $(value).text('');
      });
      $.each(input, function(index, value) {
        $(value).removeClass('is-invalid');
      });
      $('#formTambahKategori').trigger('reset');
    });
  })

  function generateSlug(event = null) {
    if(!event) {
        let value = $('#title').val();
        value = value.toLowerCase().split(' ').join('-');
        $('#slug').val(value);
    }else {
        let value = event.target.value;
        value = value.toLowerCase().split(' ').join('-');
        $('#slug').val(value);
    }
  }

  function storeKategori() {
    return new Promise((resolve, reject) => {
        $.post('{{ route("admin.artikel.store-kategori") }}', {
            nama: $('#namaKategori').val(),
            alias: $('#alias').val(),
            order: $('#order').val(),
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            resolve(response);
        }).fail(function(err) {
            reject(err);
        })
    })
  }

  function getViewKategori() {
    return new Promise((resolve, reject) => {
        $.get('{{ route("admin.artikel.view-kategori") }}').done(response => {
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