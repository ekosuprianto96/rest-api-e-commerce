<select class="form-control form-control-sm" name="kategori_id" id="kategori">
    <option value="">-- Pilih Kategori --</option>
    @foreach($kategori as $key => $value)
        <option value="{{ $value->id }}">{{ $value->nama }}</option>
    @endforeach
</select>