@extends('layouts.main', ['title' => 'Tambah Menu'])

@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
          <div class="w-100 h-100">
            <a href="javascript:void(0)" class="btn btn-sm btn-danger">Kembali</a>
          </div>
        </div>
        <div class="col-md-6 text-right">
            <h1 style="font-size: 1.5em">Tambah Menu</h1>
          </div>
      </div>
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.ms-menu.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Nama</label>
                    <input type="text" value="{{ old('nama_menu') }}" name="nama_menu" class="form-control form-control-sm @error('nama_menu') is-invalid  @enderror" placeholder="nama menu...">
                    @error('nama_menu')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Nama Alias</label>
                    <input type="text" value="{{ old('nama_alias') }}" name="nama_alias" class="form-control @error('nama_alias') is-invalid  @enderror form-control-sm" placeholder="nama alias...">
                    @error('nama_alias')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">URL</label>
                    <input type="text" value="{{ old('url') }}" name="url" class="form-control @error('url') is-invalid  @enderror form-control-sm" placeholder="url...">
                    @error('url')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Icon</label>
                    <input type="text" value="{{ old('icon') }}" name="icon" class="form-control @error('icon') is-invalid  @enderror form-control-sm" placeholder="icon...">
                    @error('icon')
                    <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Parent</label>
                    <select class="form-control @error('parent') is-invalid  @enderror form-control-sm" name="parent" id="parent">
                        <option value="">-- Pilih Parent --</option>
                        @foreach(App\Models\MenuParent::all() as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->nama }}</option>
                        @endforeach
                    </select>
                    @error('parent')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Status</label>
                    <select class="form-control @error('status') is-invalid  @enderror form-control-sm" name="status" id="status">
                        <option value="">-- Pilih Status --</option>
                       <option value="1">Aktif</option>
                       <option value="0">Non Aktif</option>
                    </select>
                    @error('status')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <button class="btn btn-sm btn-success">Simpan</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
 
</script>
@endsection