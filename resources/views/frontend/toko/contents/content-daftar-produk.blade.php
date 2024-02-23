@php
    $user = Auth::user();
@endphp
<div class="py-3">
    <div class="flex items-center">
        <div class="w-1/2">
            <h4 class="font-bold">{{ $title }}</h4>
        </div>
        <div class="w-1/2 flex justify-end items-center">
            <a href="{{ route('toko.upload-produk') }}" class="px-6 py-2 text-sm w-max text-center bg-green-500 rounded-md block text-slate-50">Upload Produk</a>
        </div>
    </div>

    @if(@count($produk) > 0)
        {{-- <ul class="px-3 mt-4 max-h-[720px] overflow-y-auto">
            @foreach($produk as $key => $value)
                <li class="flex flex-col lg:flex-row mb-2 py-2 border-b-2 lg:pl-4 relative">
                    <div class="flex gap-2 w-full py-2 lg:py-0 items-start">
                        <div style="background-image: url({{ $value->images[0]->url ?? config('app.logo') }})" class="w-[150px] border lg:h-full h-[100px] bg-cover bg-center">

                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-bold text-sm">{{ $value->nm_produk }} <span class="text-[0.6em] {{ $value->type_produk == 'MANUAL' ? 'bg-blue-500' : 'bg-orange-500' }} ms-2 px-2 py-1 rounded-lg text-slate-50">{{ $value->type_produk }}</span></h3>
                            <div class="text-xs flex lg:py-2 items-center gap-2">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                            <div class="text-xs mb-2 flex items-center gap-3">
                                <span class="bg-blue-400 text-xs px-2 py-1 rounded-lg text-slate-50">{{ $value->kategori->nama_kategori }}</span> |
                                <span class="text-xs block text-slate-500">Terjual : {{ $value->total_terjual }}</span>
                            </div>
                            <span class="text-sm">Rp. {{ $value->harga['harga_fixed'] }}</span>
                            @if($value->potongan)
                                <span class="block mt-3 rounded-lg w-max px-2 bg-red-400 text-white text-xs">Disk : Rp. {{ $value->potongan }}</span>
                            @endif
                            @if($value->status_confirm == '0')
                                <span class="block text-xs bg-red-500 text-slate-50 rounded-md px-3 py-1 mt-3">Sedang Di Moderasi Oleh Admin</span>
                            @else
                                <span class="block text-xs bg-green-500 text-slate-50 rounded-md px-3 py-1 mt-3 w-max">Telah Di Konfirmasi</span>
                            @endif
                        </div>
                    </div>
                    <div class="lg:absolute right-0 flex items-center lg:justify-start justify-end gap-2 bottom-2">
                        <button type="button" class="px-4 py-1 bg-green-500 text-slate-50 text-sm rounded-md">Edit</button>
                        <button type="button" class="px-4 py-1 bg-red-500 text-slate-50 text-sm rounded-md">Hapus</button>
                    </div>
                </li>
            @endforeach
        </ul> --}}

        <div class="flex items-center flex-col mt-4 lg:flex-row">
            <div class="flex w-full justify-between lg:justify-start items-center lg:w-1/2">
                <span class="text-[1em] lg:order-2 font-semibold lg:ms-2 block">Filter</span>
                <button id="filterTypeProduk" type="button" class="bg-blue-500 filter-btn lg:order-1 text-slate-50 rounded-lg py-1 p-2 relative">
                <i class="ri-equalizer-fill text-[1.1em] filter-btn"></i>
                <ul style="display: none" class="absolute border top-[110%] overflow-hidden right-0 lg:left-0 z-[40] w-max bg-slate-50 rounded-lg" style="box-shadow: 0px 0px 4px rgba(48, 48, 48, 0.26);">
                    <li id="semuaProduk" class="whitespace-nowrap text-slate-50 bg-blue-400 hover:bg-blue-400 hover:text-slate-50 w-full border">
                        <span class="text-start block filter-btn w-full px-3 py-2">Semua Produk</span>
                    </li>
                    <li id="produkAuto" class="whitespace-nowrap text-black hover:bg-blue-400 hover:text-slate-50 w-full border">
                        <span class="text-start  block filter-btn w-full px-3 py-2">Produk Auto</span>
                    </li>
                    <li id="produkManual" class="whitespace-nowrap text-black hover:bg-blue-400 hover:text-slate-50 w-full">
                        <span class="text-start  block filter-btn w-full px-3 py-2">Produk Manual</span>
                    </li>
                </ul>
                </button>
            </div>
            <div class="w-full py-2 lg:w-1/2">
                <div class="w-full h-max flex items-center relative">
                    <input id="inputSearch" type="text" class="border w-full focus:outline-none focus:shadow-md focus:border-blue-500 h-[40px] rounded-full text-sm px-3" placeholder="Cari produk...">
                    <span class="absolute hover:cursor-pointer hover:bg-blue-500 right-1 flex justify-center items-center rounded-full bg-slate-300 w-[30px] h-[30px]">
                        <i class="ri-search-line text-slate-50"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="overflow-y-auto overflow-x-auto h-[400px] min-h-max overflow-hidden rounded-lg shadow-inner">
                <table id="tableOrder" class="display w-full">
                    <thead class="bg-blue-300 text-slate-50">
                        <tr class="sticky">
                            <th class="text-xs lg:table-cell hidden p-3">#</th>
                            <th class="text-xs p-3">Produk</th>
                            <th class="text-xs lg:table-cell hidden p-3">Harga</th>
                            <th class="text-xs lg:table-cell hidden p-3">Kategori</th>
                            <th class="text-xs lg:table-cell hidden p-3">Tanggal Upload</th>
                            <th class="text-xs p-3">Status</th>
                            <th class="text-xs p-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-slate-100">
                        @if(@count($produk) > 0)
                            @foreach($produk as $key => $value)    
                                <tr class="hover:bg-slate-200">
                                    <td class="text-xs text-center lg:table-cell hidden p-3">{{ $key + 1 }}</td>
                                    <td class="text-xs p-3">
                                        <div class="flex w-[200px]">
                                            <img src="{{ $value->images[0]->url ?? config('app.logo') }}" class="rounded-lg me-2" width="80" alt="">
                                            <div class="flex flex-col">
                                                <span title="{{ $value->nm_produk }}" class="block nama_produk truncate mb-2 hover:text-wrap overflow-hidden max-w-[100px] lg:max-w-[200px]">{{ $value->nm_produk }}</span>
                                                <span class="{{ $value->type_produk === 'MANUAL' ? 'text-green-500' : 'text-red-500' }} type_produk">Produk : {{ $value->type_produk }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-xs text-center lg:table-cell hidden p-3">
                                        <div class="flex justify-center items-center flex-col">
                                            <span>Rp. {{ $value->harga['harga_real'] }}</span>
                                            @if($value->potongan_persen > 0)
                                                <span class="block text-xs bg-red-200 text-red-500 rounded-md px-3 w-max py-1 mt-3">Disc : {{ number_format($value->potongan_persen, 0) }}%</span>
                                            @elseif($value->potongan_harga > 0)
                                                <span class="block text-xs bg-red-200 text-red-500 rounded-md px-3 w-max py-1 mt-3">Disc : Rp. {{ number_format($value->potongan_harga, 0, 0, '.') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-xs text-center lg:table-cell hidden p-3">{{ $value->kategori->nama_kategori }}</td>
                                    <td class="text-xs text-center lg:table-cell hidden p-3">{{ \Carbon\carbon::parse($value->created_at)->format('d M Y') }}</td>
                                    <td class="text-xs text-center p-3">
                                        @if($value->status_confirm == '0')
                                            <span class="text-[0.6em] text-nowrap bg-red-500 text-slate-50 rounded-md px-3 py-1 mt-3">Sedang Di Moderasi Oleh Admin</span>
                                        @else
                                            <span class="text-xs text-nowrap bg-green-500 text-slate-50 rounded-md px-3 py-1 mt-3 w-max">Telah Di Konfirmasi</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-center p-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('toko.produk.edit', $value->kode_produk) }}" class="text-green-500" title="Edit Produk">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            <a href="javascript:void(0)" data-produk-id="{{ $value->kode_produk }}" class="text-red-500 deleteProduk" title="Hapus Produk">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="data-not-found">
                                <td colspan="7" class="text-center text-sm p-4">Produk Tidak Ditemukan</td>
                            </tr>
                        @endif
                        <tr class="data-not-found" style="display: none">
                            <td colspan="8" class="text-center text-sm p-4">Produk Tidak Ditemukan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="min-w-full mt-4 lg:h-full gap-3 py-3 border overflow-hidden rounded-lg flex justify-center flex-col items-center">
            <div class="w-full flex justify-center items-center p-3">
                <img src="{{ asset('assets/frontend/images/tidak-ada-produk.svg') }}" width="250" alt="Tidak Ada Produk">
            </div>
            <div>
                <p class="font-bold text-[1em] text-center">Kamu Belum Memiliki Produk, Upload Produkmu Sekarang Dan Dapatkan Penghasilan.</p>
            </div>
            <div class="w-full py-2 px-3">
                <a href="{{ route('toko.upload-produk') }}" class="px-6 py-2 text-sm text-center bg-blue-600 rounded-md w-full block text-slate-50">Upload Produk</a>
            </div>
        </div>
    @endif
</div>

{!! renderScript('script-order-toko') !!}
<script>
    $(function() {
        const deleteProduk = $('.deleteProduk');

        $.each(deleteProduk, function(index, value) {
            $(value).click(function(event) {
                const dataIdProduk = $(this).attr('data-produk-id');
                Swal.fire({
                    title: "Perhatian!",
                    text: 'Apakah anda yakin akan menghapus produk ini ?',
                    icon: "warning",
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    buttonsStyling: true,
                    showCancelButton: true,
                    confirmButtonText: '<form action="{{ route("toko.produk.destroy") }}" method="post">@csrf<input name="kodeproduk" type="hidden" value="'+dataIdProduk+'" /><button type="submit">Ya, Lanjutkan</button></form>',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if(!result.isConfirmed) {
                    return false;
                    }
                })
            })
        })
    })
</script>


