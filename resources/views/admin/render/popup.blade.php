<div class="w-100">
    <div class="row">
        <div class="col-md-6">
            <h5>Notifikasi Popup</h5>
        </div>
        <div class="col-md-6 text-right">
            @if(isset($popup) && $popup->an)
            <span class="badge badge-sm badge-success">Aktif</span>
            @else
            <span class="badge badge-sm badge-danger">Tidak Aktif</span>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="">Title</label>
            <input id="popup-title" type="text" value="{{ $popup->title }}" placeholder="Title..." name="popup-title" class="form-control form-control-sm">
        </div>
        <div class="col-md-12 mb-3">
            <label for="">Content</label>
            <textarea name="popup-content" id="summernotePopup" cols="30" rows="10">{{ $popup->content }}</textarea>
        </div>
        <div class="col-md-12 mb-3">
            <label for="">Status</label>
            <select class="form-control form-control-sm" name="popup-status" id="popupStatus">
                <option {{ $popup->an ? 'selected' : '' }} value="1">Aktif</option>
                <option {{ !$popup->an ? 'selected' : '' }} value="0">Non Aktif</option>
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <button type="button" id="simpanNotifikasi" class="btn btn-sm btn-success">Simpan</button>
        </div>
    </div>
</div>

<script>
    $('#summernotePopup').summernote({
        heigt: 200,
        minHeight: 200,
        maxHeight: 200
    });

    $(function() {
        $('#simpanNotifikasi').click(function() {
            postNotifikasiPopup().then(response => {
                $.toast({
                    heading: 'Sukses',
                    text: response.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'success'
                });
                getViewPopup().then(response => {
                    $('#skeletonPopup').hide();
                    $('#notifikasiPopup').show();
                    $('#notifikasiPopup').html(response);
                }).catch(err => {
                    $.toast({
                        heading: 'Error',
                        text: err.message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                });
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

    function postNotifikasiPopup() {
        return new Promise((resolve, reject) => {
            $.post('{{ route("admin.notifikasi.update") }}', {
                type: 1,
                _token: '{{ csrf_token() }}',
                title: $('#popup-title').val(),
                content: $('#summernotePopup').val(),
                status: $('#popupStatus').val()
            }).done(function(response) {
                resolve(response);
            }).fail(err => {
                reject(err);
            })
        })
    }
</script>