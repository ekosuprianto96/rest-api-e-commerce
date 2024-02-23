@php
    $user = Auth::user();
    $penjualan = explode('.', $penghasilan['total_penjualan']);

    if(strlen($penjualan[0]) >= 3) {
        $penjualan = $penjualan[0].' jt';
    }else {
        $penjualan = $penghasilan['total_penjualan'];
    }
@endphp
<div class="py-3 h-max overflow-hidden">
    <h4 class="font-bold">{{ $title }}</h4>

    <div class="grid mt-4 gap-2 grid-cols-2 mb-3">
        <div title="Rp. {{ $penghasilan['total_penjualan'] }}" class="p-2 border rounded-md hover:cursor-pointer">
            <span class="text-sm font-semibold block mb-2">Total Penjualan</span>
            <span class="text-[1.1em] font-semibold block">Rp. {{ $penjualan }}</span>
        </div>
        <div class="p-2 border rounded-md">
            <span class="text-sm font-semibold mb-2 block">Total Terjual</span>
            <span class="text-[1.2em] font-semibold block">{{ $penghasilan['total_terjual'] }}</span>
        </div>
    </div>
    <div class="flex items-center flex-col lg:flex-row">
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
    <div>
        @if(@count($penghasilan['dataOrder']) > 0)
            <div class="mt-3">
                <h4 class="mb-3">Daftar Semua Order</h4>
                <div class="overflow-y-auto overflow-x-auto h-[400px] min-h-max overflow-hidden rounded-lg shadow-inner">
                    <table id="tableOrder" class="display w-full">
                        <thead class="bg-blue-300 text-slate-50">
                            <tr class="sticky">
                                <th class="text-xs lg:table-cell hidden p-3">#</th>
                                <th class="text-xs p-3">Produk</th>
                                <th class="text-xs p-3">Type Produk</th>
                                <th class="text-xs lg:table-cell hidden p-3">Total Dibayar</th>
                                <th class="text-xs lg:table-cell hidden p-3">Nama Pembeli</th>
                                <th class="text-xs lg:table-cell hidden p-3">Tanggal</th>
                                <th class="text-xs p-3">Status Order</th>
                                <th class="text-xs p-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-100">
                            @if(@count($penghasilan['dataOrder']) > 0)
                                @foreach($penghasilan['dataOrder'] as $key => $value)    
                                    <tr class="hover:bg-slate-200">
                                        <td class="text-xs text-center lg:table-cell hidden p-3">{{ $key + 1 }}</td>
                                        <td class="text-xs p-3">
                                            <div class="flex w-[200px]">
                                                <img src="{{ $value['produk']['image'] }}" class="rounded-lg me-2" width="80" alt="">
                                                <div class="flex flex-col">
                                                    <span title="{{ $value['produk']['nama_produk'] }}" class="block nama_produk truncate hover:text-wrap overflow-hidden max-w-[100px]">{{ $value['produk']['nama_produk'] }}</span>
                                                    <span class="block truncate no_order overflow-hidden max-w-[100px]">{{ trim($value['order']['no_order']) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-xs type_produk text-center p-3">{{ $value['produk']['type_produk'] }}</td>
                                        <td class="text-xs text-center lg:table-cell hidden p-3">Rp. {{ $value['order']['total_biaya'] }}</td>
                                        <td class="text-xs text-center lg:table-cell hidden p-3">{{ $value['pembeli']['nama_pembeli'] }}</td>
                                        <td class="text-xs text-center lg:table-cell hidden p-3">{{ \Carbon\carbon::parse($value['order']['tanggal'])->format('d M Y') }}</td>
                                        <td class="text-xs text-center {{ ($value['order']['status_order'] == '0' ? 'text-red-500' : ($value['order']['status_order'] == 'SUCCESS' ? 'text-green-500' : ($value['order']['status_order'] == 'CANCEL' ? 'text-red-500' : 'text-orange-400'))) }} }} p-3">{{ ($value['order']['status_order'] == '0' ? 'Belum selesai' : ($value['order']['status_order'] == 'SUCCESS' ? 'Selesai' : ($value['order']['status_order'] == 'CANCEL' ? 'Cancel' : 'Pending'))) }}</td>
                                        <td class="text-xs text-center p-3">
                                            <a href="{{ route('toko.order.detail').'?no_order='.$value['order']['no_order'].'&vx='.$value['order']['id'] }}" class="bg-blue-500 rounded-lg text-[0.8em] px-2 py-1 text-slate-50">Lihat</a>
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
            <div class="w-full">
                <div class="flex flex-col justify-center items-center p-6 h-full rounded-lg">
                    <img src="{{ asset('assets/frontend/images/no-cart.svg') }}" class="h-[300px]" alt="Transaksi Anda Masih Kosong">
                    <div class="py-3">
                        <span class="font-bold lg:text-lg text-sm">Orderan Kamu Masih Kosong</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{!! renderScript('script-order-toko') !!}

