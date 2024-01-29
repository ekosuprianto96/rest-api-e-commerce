<div class="w-100">
    <div class="row">
        <div class="col-md-6">
            <h5>Notifikasi Bar Top</h5>
        </div>
        <div class="col-md-6 text-right">
            @if(isset($bartop) && $bartop->an)
            <span class="badge badge-sm badge-success">Aktif</span>
            @else
            <span class="badge badge-sm badge-danger">Tidak Aktif</span>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="">Title</label>
            <input id="bartop-title" type="text" value="{{ $bartop->title }}" placeholder="Title..." name="bartop-title" class="form-control form-control-sm">
        </div>
        <div class="col-md-12 mb-3">
            <label for="">Content</label>
            <textarea name="bar-content" id="summernoteBar" cols="30" rows="10">{{ $bartop->content }}</textarea>
        </div>
        <div class="col-md-12 mb-3">
            <label for="">Status</label>
            <select class="form-control form-control-sm" name="bartop-status" id="barTopStatus">
                <option {{ $bartop->an ? 'selected' : '' }} value="1">Aktif</option>
                <option {{ !$bartop->an ? 'selected' : '' }} value="0">Non Aktif</option>
            </select>
        </div>
        <div class="col-md-12 text-right mb-3">
            <button type="button" id="simpanNotifikasiBartop" class="btn btn-sm btn-success">Simpan</button>
        </div>
    </div>
</div>

<script>
     $('#summernoteBar').summernote({
        heigt: 200,
        minHeight: 200,
        maxHeight: 200
    });

    $(function() {
        $('#simpanNotifikasiBartop').click(function() {
            postNotifikasiBartop().then(response => {
                $.toast({
                    heading: 'Sukses',
                    text: response.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'success'
                });
                getViewBartop().then(response => {
                    $('#skeletonBartop').hide();
                    $('#notifikasiBartop').show();
                    $('#notifikasiBartop').html(response);
                }).catch(err => {
                    $.toast({
                        heading: 'Error',
                        text: err.message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                })
            }).catch(err => {
                $.toast({
                    heading: 'Error',
                    text: err.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
            })
        });
        
    });

    function postNotifikasiBartop() {
        return new Promise((resolve, reject) => {
            $.post('{{ route("admin.notifikasi.update") }}', {
                type: 2,
                _token: '{{ csrf_token() }}',
                title: $('#bartop-title').val(),
                content: $('#summernoteBar').val(),
                status: $('#barTopStatus').val()
            }).done(function(response) {
                resolve(response);
            }).fail(err => {
                reject(err);
            })
        })
    }
</script>