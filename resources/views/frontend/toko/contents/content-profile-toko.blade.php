<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>

    <div style="background-image: url({{ asset('assets/frontend/images/bg-seller.jpg') }})" class="rounded-lg mt-4 mb-4 bg-center bg-cover w-full min-h-[100px] overflow-visible lg:mb-8 border relative flex justify-center bg-blue-600">
        <div class="w-[80px] flex justify-center items-center h-[80px] overflow-hidden rounded-full bg-cover bg-center border-white absolute -bottom-6">
            <img id="imageProfile" src="{{ $toko->image ?? config('app.logo') }}" class="h-full absolute z-[30]" alt="Logo">
            <div class="w-full h-full relative flex justify-center items-center">
                <input id="inputImageProfile" type="file" class="opacity-0 relative z-50 w-full h-full">
                <i id="iconCamera" class="ri-camera-fill absolute z-[40]"></i>
                <div id="loader" style="display: none" class="absolute z-[40] flex justify-center items-center">
                    <svg aria-hidden="true" role="status" class="inline w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div class="py-4">
        <div class="px-2">
          <span class="font-bold text-lg text-blue-600">Saldo : Rp. {{ $toko->saldo->total_saldo }}</span>
        </div>
        <div class="px-2">
            <span class="font-bold text-lg text-orange-500">Saldo Clearing: Rp. {{ $toko->saldo_refaund->total_refaund }}</span>
            <i class="ri-history-line ms-2"></i>
            <button class="ms-1" data-popover-target="infoSaldoClearing" type="button"><i class="ri-information-fill hover:cursor-pointer hover:text-blue-600"></i></button>
            <div data-popover id="infoSaldoClearing" role="tooltip" class="absolute z-[90] invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
                <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Apa Itu Saldo Clearing?</h3>
                </div>
                <div class="px-3 py-2">
                    <p>Saldo clearing adalah saldo yang masih ditangguhkan dan akan masuk ke saldo utama selama 3 x 24 jam.</p>
                </div>
                <div data-popper-arrow></div>
            </div>
        </div>
    </div>
    <div class="w-full">
        <div class="py-2 lg:w-full">
            <span class="text-sm">Link Toko
                    <button class="ms-1" data-popover-target="linkToko" type="button"><i class="ri-information-fill hover:cursor-pointer hover:text-blue-600"></i></button>
                    <div data-popover id="linkToko" role="tooltip" class="absolute z-[90] invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
                        <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Link Toko?</h3>
                        </div>
                        <div class="px-3 py-2">
                            <p>Ini adalah link toko anda yang bisa dibagikan ke media sosial</p>
                        </div>
                        <div data-popper-arrow></div>
                    </div>
            </span>
            <div class="w-full flex items-center rounded relative border overflow-hidden h-max">
                <input id="dataLinkToko" value="{{ route('user.dashboard', getUserName()) }}" type="text" readonly class="w-full bg-slate-200 focus:outline-none bg-none px-4 py-2 lg:text-sm text-[0.6em]" value="">
                <button id="btnCopyLink" class="absolute right-0 text-slate-50 bg-blue-600 p-4 text-sm">Copy Link</button>
            </div>
        </div>
        <form action="{{ route('toko.settings.update', $toko->kode_toko) }}" method="POST">
            @csrf
            @method('put')
            <div class="relative overflow-hidden mb-3 w-full">
                <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Nama Toko</label>
                <input name="nama_toko" required value="{{ $toko->nama_toko }}" type="text" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
            </div>
            <div class="relative overflow-hidden mb-3 w-full">
                <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Alamat Toko</label>
                <input name="alamat_toko" required value="{{ $toko->alamat_toko }}" type="text" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
            </div>
            <div class="relative overflow-hidden mb-3 w-full">
                <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Email</label>
                <input name="email" required readonly value="{{ $toko->user->email }}" type="text" class="w-full border rounded-lg focus:border-red-500 bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
            </div>
            <div class="relative overflow-hidden mb-3 w-full">
                <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">No Hape Toko</label>
                <input name="no_hape" readonly value="{{ $toko->user->no_hape }}" type="text" class="w-full border rounded-lg focus:border-red-500 bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
            </div>
            <div class="relative overflow-hidden mb-3 w-full">
                <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Jam Buka</label>
                <input name="jam_buka" value="{{ $toko->jam_buka }}" type="time" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
            </div>
            <div class="relative overflow-hidden mb-3 w-full">
                <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Jam Tutup</label>
                <input type="time" value="{{ $toko->jam_tutup }}" name="jam_tutup" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
            </div>
            <div class="relative overflow-hidden mb-3 w-full">
                <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Jam Tutup</label>
                <textarea name="deskripsi_toko" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">{{ $toko->deskripsi_toko }}</textarea>
            </div>
            <div class="py-4">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-slate-50 rounded-md w-full">Simpan</button>
                {{-- <button v-else type="button" :disabled="is_loading" @click="handleSubmit" class="px-3 text-center py-2 bg-blue-600 rounded-md text-slate-50 w-full">
                <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                </svg>
                Loading
                </button> --}}
            </div>
        </form>
      </div> 
</div>

<script>
    $(function() {
        $('#btnCopyLink').click(function(event) {
            const dataLinkToko = $('#dataLinkToko').select();

            document.execCommand('copy');
        })

        $('#inputImageProfile').change(function(event) {
            const file = event.target.files[0];
            const url = URL.createObjectURL(file);

            $('#loader').show();
            $('#iconCamera').hide();

            storeIamge(file).then(response => {
                const {status, image, error, message} = response;

                if(status && !error) {
                    $('#loader').hide();
                    $('#iconCamera').show();
                    $('#imageProfile').attr('src', image);

                    Swal.fire({
                        title: "Sukses!",
                        text: message,
                        icon: "success",
                        allowOutsideClick: false,
                        buttonsStyling: true,
                        showCloseButton: true,
                    });

                    return;
                }

                $('#loader').hide();
                $('#iconCamera').show();
                Swal.fire({
                    title: "Gagal!",
                    text: message,
                    icon: "warning",
                    allowOutsideClick: false,
                    buttonsStyling: true,
                    showCloseButton: true,
                });

                return;
            }).catch(err => {
                $('#loader').hide();
                $('#iconCamera').show();

                Swal.fire({
                    title: "Gagal!",
                    text: err.message,
                    icon: "warning",
                    allowOutsideClick: false,
                    buttonsStyling: true,
                    showCloseButton: true,
                });

                return;
            })
        })
    });

    function storeIamge(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        return new Promise((resolve, reject) => {
            $.ajax({
                method: 'post',
                contentType: false,
                processData: false,
                cache: false,
                data: formData,
                enctype: 'multipart/form-data',
                url: '{{ route("toko.settings.upload-image") }}'
            })
            .done(function(response) {
                resolve(response);
            }).fail(function(err) {
                reject(err);
            })
        })
    }
</script>
