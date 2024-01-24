@extends('layouts.main', ['title' => 'Setting Menu'])
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
          <div class="w-100 h-100">
            <a href="{{ route('admin.ms-menu.index') }}" class="btn btn-sm btn-danger">Kembali</a>
          </div>
        </div>
        <div class="col-md-6 text-right">
            <h1 style="font-size: 1.5em">Setting Menu</h1>
          </div>
      </div>
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.ms-menu.update', $menu->id) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Nama</label>
                    <input type="text" readonly value="{{ old('nama_menu') ?? $menu->nama }}" name="nama_menu" class="form-control form-control-sm @error('nama_menu') is-invalid  @enderror" placeholder="nama menu...">
                    @error('nama_menu')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Nama Alias</label>
                    <input type="text" readonly value="{{ old('nama_alias') ?? $menu->nama_alias }}" name="nama_alias" class="form-control @error('nama_alias') is-invalid  @enderror form-control-sm" placeholder="nama alias...">
                    @error('nama_alias')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">URL</label>
                    <input type="text" readonly value="{{ old('url') ?? $menu->url }}" name="url" class="form-control @error('url') is-invalid  @enderror form-control-sm" placeholder="url...">
                    @error('url')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Icon</label>
                    <input type="text" readonly value="{{ old('icon') ?? $menu->icon }}" name="icon" class="form-control @error('icon') is-invalid  @enderror form-control-sm" placeholder="icon...">
                    @error('icon')
                    <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Parent</label>
                    <select class="form-control @error('parent') is-invalid  @enderror form-control-sm" name="parent" id="parent">
                        <option value="">-- Pilih Parent --</option>
                        @foreach(App\Models\MenuParent::all() as $key => $value)
                            <option {{ $menu->id_parent == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->nama }}</option>
                        @endforeach
                    </select>
                    @error('parent')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="" class="label-form-control">Status</label>
                    <select disabled class="form-control @error('status') is-invalid  @enderror form-control-sm" name="status" id="status">
                        <option value="">-- Pilih Status --</option>
                       <option {{ $menu->an == 1 ? 'selected' : '' }} value="1">Aktif</option>
                       <option {{ $menu->an == 0 ? 'selected' : '' }} value="0">Non Aktif</option>
                    </select>
                    @error('status')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-12">
                    <hr>
                    <h4 class="mb-3">Akses Role</h4>
                    <div class="table-responsive table-striped">
                        <table class="table">
                            <thead>
                                <th style="width: 20px;">#</th>
                                <th>Nama Role</th>
                                <th>Pilih</th>
                            </thead>
                            <tbody>
                                @php
                                    $index = 0;
                                @endphp
                                @foreach($roles as $key => $value)
                                    <tr>
                                        <td>{{ $index += 1 }}</td>
                                        <td align="center">{{ $value->name }}</td>
                                        <td align="center">
                                            <input type="hidden" value="{{ empty($menu_roles[$value->id]) ? '' : ($menu_roles[$value->id]->id == $value->id ? $value->id : '')}}" id="role_{{ $value->id }}" name="roles[]">
                                            <input type="checkbox" onclick="setValueRole(event, '{{ $value->id }}')" {{ empty($menu_roles[$value->id]) ? "" : ($menu_roles[$value->id]->id == $value->id ? "checked" : "")}}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
 $(function() {

 })

 function setValueRole(event, id_role) {
     if($(`#role_${id_role}`).val() != '') {
         $(`#role_${id_role}`).val('');
    }else {
        $(`#role_${id_role}`).val(id_role);
    }
        console.log($(`#role_${id_role}`).val())
 }
</script>
@endsection