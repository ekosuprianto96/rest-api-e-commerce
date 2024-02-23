<link href="{{ asset('assets/admin/summernote/summernote-lite.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/admin/summernote/summernote-lite.min.js') }}"></script>
<script>
    arrayImages = [];

    $(function() {
        $('#deskripsi').summernote({
            height: 200
        })

        $('#diskonPersen').click(function(event) {
            initTab(1);
        })

        $('#diskonHarga').click(function(event) {
            initTab(2);
        });

        $('#inputImage').change(function(event) {
            const image = event.target.files[0];
            const url = URL.createObjectURL(image);
            const objectImage = {
                url: url,
                uuid: null
            };

            

            storeImage(image).then((result) => {
                const {status, error, message, detail} = result;

                if(status) {
                    objectImage.uuid = detail;
                    arrayImages.push(objectImage);

                    renderImage();
                }
            }).catch((err) => {
                console.log(err)
            });

        });

    })
    
    

    function terapkanDiskon(event = null, an, tab = 1) {
        
        if(event) {
            const findInput = $('.input-diskon').length;
            
            if(findInput <= 0) {
                renderInput(event);
                initTab(tab);
                $('#groupDiskon').show();
            }else {
                $('#potonganHarga').val(null).removeAttr('required');
                $('#potonganPersen').val(null).removeAttr('required');
                $('#groupDiskon').hide();
                $('.input-diskon').remove();
            }

            return;
        }

    }
    function renderInput(event) {
        $(event).append('<input class="input-diskon" type="hidden" value="1" nama="status_diskon">');
    }

    function initTab(tab) {
        if(tab == 1) {
            $('#diskonPersen').show().addClass('bg-blue-600 text-slate-50');
            $('#diskonHarga').show().removeClass('bg-blue-600 text-slate-50');
            $('#inputPotonganPersen').show();
            $('#inputPotonganHarga').hide();
            $('#potonganPersen').attr('required', true);
            $('#potonganHarga').val(null).removeAttr('required');
            $('#typePotongan').val(1);

            return;
        }

        $('#diskonHarga').show().addClass('bg-blue-600 text-slate-50');
        $('#diskonPersen').show().removeClass('bg-blue-600 text-slate-50');
        $('#inputPotonganHarga').show();
        $('#inputPotonganPersen').hide();
        $('#potonganHarga').attr('required', true);
        $('#potonganPersen').val(null).removeAttr('required');
        $('#typePotongan').val(2);
        return;
    }

    // function deleteImage() {
    //     const listImages = $('.list-image');
        
    //     $.each(listImages, function(index, value) {
    //         // const currentIndex = arrayImages.indexOf(index);
    //         $(value).click(function(event) {
    //             alert('ok')
    //             arrayImages.slice(index, 1);
    //         })
    //     })
    // }

    function renderImage() {     
        $('#wrapperPreviewImages').html('')
        
        if(arrayImages.length > 0) {
            $('#wrapperPreviewImages').addClass('w-full lg:min-w-max min-w-full max-w-max')
        }

        $.each(arrayImages, function(index, value) {
            const image = {
                url: value.url,
                uuid: value.uuid,
                index
            };
            
            $('#wrapperPreviewImages').append(createPrevImage(image));
        });
    }

    function createPrevImage({url, index, uuid}) {
        return `<div class="h-[140px] w-full border relative z-40 lg:h-[160px] p-2 lg:w-[200px]">
                    <div style="background-image: url(${url})" class="bg-slate-300 bg-cover bg-center w-full border z-50 h-full rounded-lg relative z-40">
                        <input type="hidden" name="images[]" value="${uuid}">
                        <button onclick="deleteImage(${index})" type="button" class="absolute z-50 imageList bg-red-500 text-slate-50 top-2 right-2 shadow-lg opacity-75 hover:opacity-100 rounded-full w-[30px] h-[30px]">
                            <i class="ri-delete-bin-5-fill"></i>
                        </button>
                    </div>
                </div>`;
    }

    function defaultCard() {
        return `<div id="defaultCard" class="w-1/2 min-h-[120px] lg:min-h-[130px] p-2 lg:w-[25%]">
                    <div class="bg-slate-300 w-full h-full rounded-lg hover:cursor-pointer hover:bg-slate-200 flex justify-center items-center relative">
                        <i class="ri-add-fill text-[2em] text-slate-400"></i>
                        <input type="file" id="inputImage" accept="image/*" class="absolute hover:cursor-pointer z-50 opacity-0 top-0 left-0 bottom-0 right-0">
                    </div>
                </div>`;
    }

    function deleteImage(index) {
        arrayImages.splice(index, 1);

        renderImage()
    }

    function storeImage(image) {
        return new Promise((resolve, reject) => {
            const fromData = new FormData();
            fromData.append('image', image);
            fromData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                method: 'post',
                contentType: false,
                processData: false,
                cache: false,
                data: fromData,
                enctype: 'multipart/form-data',
                url: '{{ route("toko.produk.store-image") }}'
            })
            .done(function(response) {
                resolve(response);
            }).fail(function(err) {
                reject(err);
            })
        })
    }

    function handleTypeProduk(type) {

        $('#typeProduk').val(type);

        if(type == 1) {
            $('#produkAuto').addClass('bg-blue-500 text-slate-50');
            $('#produkManual').removeClass('bg-blue-500 text-slate-50');

            return;
        }

        $('#produkAuto').removeClass('bg-blue-500 text-slate-50');
        $('#produkManual').addClass('bg-blue-500 text-slate-50');
    }

    function terapkanAffiliasi(event) {

        if($('#groupAffiliasi').hasClass('active')) {
            $('#groupAffiliasi').hide().removeClass('active');
            $('#groupAffiliasi').find('select').removeAttr('required').val(null);

            return;
        }

        $('#groupAffiliasi').show().addClass('active');
        $('#groupAffiliasi').find('select').attr('required', true);
        return;
    }

    function terapkanGaransi(event) {

        if($('#groupGaransi').hasClass('active')) {
            $('#groupGaransi').hide().removeClass('active');
            $('#groupGaransi').find('select').removeAttr('required').val(null);

            return;
        }

        $('#groupGaransi').show().addClass('active');
        $('#groupGaransi').find('select').attr('required', true);
        return;
    }
</script>