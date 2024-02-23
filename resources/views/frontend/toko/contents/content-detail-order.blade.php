
<div class="py-3">
    <div class="lg:h-[600px] lg:p-4 overflow-y-auto">
        <div class="text-end w-full mt-2">
            <a href="{{ route('toko.daftar-order') }}" class="px-3 py-2 text-slate-50 text-sm bg-blue-500 rounded-lg">Kembali</a>
        </div>
        <div class="mt-4">
            <div id="detailOrder" class="flex mb-3 py-3 rounded-lg px-2 transition-all hover:cursor-pointer justify-between items-center hover:bg-blue-200 bg-blue-300 text-slate-50">
                <h4 class="text-sm font-bold">Detail Order</h4>
                <i class="ri-arrow-down-s-line transition-all rotate-180" style="line-height: normal;"></i>
            </div>
            <hr class="border-dotted border-b-4 border-t-0">
            @if($order['produk']['type_produk'] == 'MANUAL')
                <div class="py-4 px-4">
                    <div class="relative flex items-center w-full justify-between">
                        <span class="{{ $order['order']['status_order'] == 'CANCEL' ? 'bg-red-500' : 'bg-green-500' }} w-[18px] z-40 h-[18px] flex items-center justify-center rounded-full relative">
                            <i class="ri-checkbox-circle-fill text-slate-50 text-xs"></i>
                            <span class="absolute top-[100%] text-xs">Pending</span>
                        </span>
                        <span class="w-[18px] {{ ($order['order']['status_order'] == 'PENDING' || $order['order']['status_order'] == 'CANCEL') ? 'bg-red-500' : ($order['order']['status_order'] == 'PROCCESS' ? 'bg-green-500' : 'bg-green-500') }} z-40 h-[18px] flex items-center justify-center rounded-full relative">
                            @if($order['order']['status_order'] == 'PROCCESS' || $order['order']['status_order'] == 'SUCCESS')
                                <i class="ri-checkbox-circle-fill text-slate-50 text-xs"></i>
                            @else
                                <i class="ri-close-circle-fill text-slate-50 text-xs"></i>
                            @endif
                            <span class="absolute top-[100%] text-xs">Proccess</span>
                        </span>
                        <span class="w-[18px] {{ ($order['order']['status_order'] == 'SUCCESS' ? 'bg-green-500' : 'bg-red-500') }} z-40 h-[18px] flex items-center justify-center rounded-full relative">
                            @if($order['order']['status_order'] == 'SUCCESS')
                                <i class="ri-checkbox-circle-fill text-slate-50 text-xs"></i>
                            @else
                                <i class="ri-close-circle-fill text-slate-50 text-xs"></i>
                            @endif
                            <span class="absolute top-[100%] text-xs">Success</span>
                        </span>
                        <div class="w-[100%] h-[3px] z-30 absolute left-0 right-0 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            @endif
            <div id="conntentDetailOrder" class="border active rounded-lg p-2 mt-4">
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-2 text-sm">No Order</td>
                            <td class="text-end py-2 text-sm">{{ $order['order']['no_order'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Tanggal</td>
                            <td class="text-end py-2 text-sm">{{ $order['order']['tanggal'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Diskon</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['produk']['diskon'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Total Biaya</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['order']['total_biaya'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Pendapatan</td>
                            <td class="text-end py-2 text-sm text-green-500">+Rp. {{ $order['order']['total_pendapatan'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Status Pembayaran</td>
                            <td class="text-end py-2">
                                <div class="w-full flex justify-end items-center">
                                    @if($order['order']['status_pembayaran'] == 0)
                                        <span class="block font-bold p-1 text-red-500 text-sm">Belum Dibayar</span>
                                    @elseif($order['order']['status_pembayaran'] == 'SUCCESS')
                                        <span class="block font-bold p-1 text-green-500 text-sm">Sukses</span>
                                    @elseif($order['order']['status_pembayaran'] == 'PENDING')
                                        <span class="block font-bold p-1 text-orange-500 text-sm">Pending</span>
                                    @elseif($order['order']['status_pembayaran'] == 'CANCEL')
                                        <span class="block font-bold p-1 text-red-500 text-sm">Cancel</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if($order['produk']['type_produk'] === 'MANUAL')
                            <tr>
                                <td class="py-2 text-sm">Status Order</td>
                                <td class="text-end py-2">
                                    <div class="w-full flex justify-end items-center">
                                        @if($order['order']['status_order'] == 0)
                                            <span class="block font-bold p-1 text-red-500 text-sm">PENDING</span>
                                        @elseif($order['order']['status_order'] == 'SUCCESS')
                                            <span class="block font-bold p-1 text-green-500 text-sm">SUCCESS</span>
                                        @elseif($order['order']['status_order'] == 'PENDING')
                                            <span class="block font-bold p-1 text-orange-500 text-sm">PENDING</span>
                                        @elseif($order['order']['status_order'] == 'CANCEL')
                                            <span class="block font-bold p-1 text-red-500 text-sm">CANCEL</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="py-2 text-sm">Metode Pembayaran</td>
                            <td class="text-end py-2 text-sm">
                                @if($order['order']['type_pembayaran'] == 'manual')
                                    <span class="text-blue-500 text-sm">(Bank Transfer)</span>
                                @endif
                                {{ $order['order']['payment'] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class=" mt-4">
            <div id="detailProduk" class="flex mb-3 py-3 rounded-lg px-2 hover:bg-blue-200 transition-all hover:cursor-pointer justify-between items-center bg-blue-300 text-slate-50">
                <h4 class="text-sm font-bold">Detail Produk</h4>
                <i class="ri-arrow-down-s-line transition-all" style="line-height: normal;"></i>
            </div>
            <hr class="border-dotted border-b-4 border-t-0">
            <div id="conntentDetailProduk" class="border rounded-lg p-2 mt-4" style="display: none">
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-2 text-sm">Nama Produk</td>
                            <td class="text-end py-2 text-sm">{{ $order['produk']['nama_produk'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Kategori</td>
                            <td class="text-end py-2 text-sm">{{ $order['produk']['kategori'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Nama Penjual</td>
                            <td class="text-end py-2 text-sm">{{ $order['order']['nama_pembeli'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Harga</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['produk']['harga_real'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Total Diskon</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['produk']['harga_diskon'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Harga Fixed</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['produk']['harga_fixed'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Type Produk</td>
                            <td class="text-end py-2 text-sm">{{ $order['produk']['type_produk'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if(@count($order['produk']['form']) > 0)
            <div id="detailForm" class="flex mb-3 py-3 rounded-lg px-2 hover:bg-blue-200 transition-all hover:cursor-pointer justify-between items-center bg-blue-300 text-slate-50">
                <h4 class="text-sm font-bold">Data Form</h4>
                <i class="ri-arrow-down-s-line transition-all" style="line-height: normal;"></i>
            </div>
            <hr class="border-dotted border-b-4 border-t-0">
            <div id="conntentDetailForm" class="border rounded-lg p-2 mt-4" style="display: none">
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-2 text-sm">Nama Produk</td>
                            <td class="text-end py-2 text-sm">{{ $order['produk']['nama_produk'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Kategori</td>
                            <td class="text-end py-2 text-sm">{{ $order['produk']['kategori'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Nama Penjual</td>
                            <td class="text-end py-2 text-sm">{{ $order['order']['nama_pembeli'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Harga</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['produk']['harga_real'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Total Diskon</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['produk']['harga_diskon'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Harga Fixed</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $order['produk']['harga_fixed'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Type Produk</td>
                            <td class="text-end py-2 text-sm">{{ $order['produk']['type_produk'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        @if($order['produk']['type_produk'] == 'MANUAL')
            <div class="mt-4">
                <form action="{{ route('toko.order.proses', $order['order']['no_order']) }}" class="flex flex-col" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <input type="hidden" name="vx" value="{{ $order['order']['vx'] }}">
                    <div class="mb-3" style="display: block">
                        <span class="text-red-500 text-[0.8em] mb-2 block">Update Status Order</span>
                        <div class="relative overflow-hidden w-full">
                            {{-- <label for="komisiAffiliasi" class="text-[0.7em] absolute top-2 left-4 text-blue-600">Total Komisi</label> --}}
                            <select name="status_order" id="updateStatusOrder" required class="w-full @error('status_order') border-red-500 @enderror overflow-hidden border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
                                <option {{ ($order['order']['status_order'] == 'SUCCESS' || $order['order']['status_order'] == 'CANCEL') ? 'disabled' : '' }} {{ ($order['order']['status_order'] == '0' || $order['order']['status_order'] == 'PENDING') ? 'selected' : '' }} value="1">Pending</option>
                                <option {{ ($order['order']['status_order'] == 'SUCCESS' || $order['order']['status_order'] == 'CANCEL') ? 'disabled' : '' }} {{ ($order['order']['status_order'] == 'PROCCESS') ? 'selected' : '' }} value="2">Proses</option>
                                <option {{ ($order['order']['status_order'] == 'SUCCESS' || $order['order']['status_order'] == 'PENDING' || $order['order']['status_order'] == 'CANCEL') ? 'disabled' : '' }} {{ ($order['order']['status_order'] == 'SUCCESS') ? 'selected' : '' }} value="3">Sukses</option>
                                <option {{ ($order['order']['status_order'] == 'SUCCESS') ? 'disabled' : '' }} {{ ($order['order']['status_order'] == 'CANCEL') ? 'selected' : '' }} value="4">Cancel</option>
                            </select>
                        </div>
                    </div>
                    <div class="" id="groupDataOrder" style="display: none">
                        <span class="text-red-500 text-[0.8em] mb-2 block">Pilih jenis data yang akan dikirim</span>
                        <ul class="flex text-sm border mb-3 rounded-ss-xl rounded-es-xl rounded-ee-xl rounded-se-xl overflow-hidden w-max">
                            <li id="dataFile" class="px-3 bg-blue-600 text-slate-50 @error('potongan_persen') bg-blue-600 text-slate-50 @enderror py-2 hover:text-slate-50 hover:bg-blue-600 hover:cursor-pointer">File</li>
                            <li id="dataText" class="px-3 py-2 @error('potongan_harga') bg-blue-600 text-slate-50 @enderror hover:text-slate-50 hover:bg-blue-600 hover:cursor-pointer">Text</li>
                        </ul>
                        {{-- Input Type Data Order --}}
                        <input type="hidden" name="type_data_order" id="typeDataOrder" value="file">
                        <div id="formFile" style="display: none" class="flex items-center justify-center mb-3 w-full">
                            <label for="inputFormFile" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik atau</span> seret file disini</p>
                                    {{-- <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p> --}}
                                </div>
                                <input {{ $order['order']['status_order'] == 'SUCCESS' ? 'disabled' : '' }} id="inputFormFile" name="file_order" type="file" class="hidden" />
                            </label>
                        </div>
                        <div id="formText" style="display: none" class="flex items-center justify-center mb-3 w-full">
                            <textarea name="text_order" placeholder="Masukkan data yang di order customer disini" id="inputFormText" class="w-full resize-none @error('status_order') border-red-500 @enderror overflow-hidden border rounded-lg focus:border-red-500 hover:bg-slate-200 h-[200px] p-4 pt-6 focus:outline-none text-sm"></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-3 py-2 bg-green-500 lg:w-max w-full text-slate-50 rounded-md">Proses Order</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    typeForm = 'file';
    oldValueProses = $('#updateStatusOrder').val();
    $(function(){      
                    
        $('#detailProduk').click(function(event) {
            if($('#conntentDetailProduk').hasClass('active')) {
                $('#conntentDetailProduk').hide().removeClass('active');
                $(this).find('i').removeClass('rotate-180');
                return;
            }

            $('#conntentDetailProduk').show().addClass('active');
            $(this).find('i').addClass('rotate-180');
            return;
        })

        $('#detailOrder').click(function(event) {
            if($('#conntentDetailOrder').hasClass('active')) {
                $('#conntentDetailOrder').hide().removeClass('active');
                $(this).find('i').removeClass('rotate-180');
                return;
            }

            $('#conntentDetailOrder').show().addClass('active');
            $(this).find('i').addClass('rotate-180');
            return;
        })

        $('#updateStatusOrder').change(function(event) {
            const value = $(this).val();

            if(value == '3') {
                $('#groupDataOrder').show();
                assignTypeDataForm('file', function(result) {
                    handleShowForm();
                });

                return;
            }else if(value == '4') {
                Swal.fire({
                    title: "Perhatian!",
                    text: 'Jika anda mengubah status order menjadi CANCEL maka anda tidak bisa lagi memproses orderan ini.',
                    icon: "warning",
                    buttonsStyling: true,
                    showCancelButton: true,
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Tidak, Batalkan'
                }).then(result => {
                    if(!result.isConfirmed) {
                        $(this).val(oldValueProses);
                        return false;
                    }

                    removeRequiredAllInput();
                    return;
                })
            }else {
                removeRequiredAllInput();
            }

            $('#groupDataOrder').hide();
            return;
        });

        $('#dataFile').click(function(event) {
            assignTypeDataForm('file', function(result) {
                handleShowForm();
            });
            
            return;
        })

        $('#dataText').click(function(event) {
            assignTypeDataForm('text', function(result) {
                handleShowForm();
            });

            return;
        })
          
    });   
    

    function handleShowForm() {
        if(typeForm === 'file') {
            $('#formFile').show().addClass('active');
            $('#formText').hide().removeClass('active');
            $('#dataFile').addClass('bg-blue-600 text-slate-50');
            $('#dataText').removeClass('bg-blue-600 text-slate-50');
        }else if (typeForm === 'text') {
            $('#formText').show().addClass('active');
            $('#formFile').hide().removeClass('active');
            $('#dataText').addClass('bg-blue-600 text-slate-50');
            $('#dataFile').removeClass('bg-blue-600 text-slate-50');
        }

        $('#typeDataOrder').val(typeForm);

        addRequiredToInput();

        return;
    }

    function assignTypeDataForm(type, callback = null) {
        typeForm = type;

        if(callback) {
            callback(type);
        }

        return type;
    }

    function addRequiredToInput() {
        if(typeForm === 'file') {
            $('#inputFormFile').attr('required', true);
            $('#inputFormText').removeAttr('required');
        }else if(typeForm === 'text') {
            $('#inputFormText').attr('required', true);
            $('#inputFormFile').removeAttr('required');
        }
        
        return;
    }

    function removeRequiredAllInput() {
        $('#inputFormText').removeAttr('required');
        $('#inputFormFile').removeAttr('required');
    }
    
</script>