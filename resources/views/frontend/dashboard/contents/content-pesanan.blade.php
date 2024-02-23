<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>

    <div class="flex items-center mt-3 flex-col lg:flex-row">
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
        @if(@count($pesanan) > 0)
            <ul class="max-h-[450px] shadow-inner overflow-y-auto pt-4 overflow-x-hidden">
                @foreach($pesanan as $key => $value)
                    <li class="flex mb-3 list-pesanan hover:cursor-pointer p-2 border rounded-md shadow-md hover:bg-slate-300 relative">
                        <div class="w-full h-[100px] grid grid-cols-12">
                            <div class="w-full col-span-4 lg:col-span-2 md:col-span-2 flex justify-center items-center overflow-hidden">
                                <img src="{{ $value->images[0]->url ?? config('app.logo') }}" class="w-full lg:w-[100px] md:w-[80px]" alt="{{ $value->produk->nm_produk }}">
                            </div>
                            <div class="w-full col-span-8 lg:col-span-10 md:col-span-10 relative py-2 px-3">
                                <span class="text-xs nama-produk block font-semibold mb-1">{{ $value->produk->nm_produk }}</span>
                                <span class="text-xs text-blue-500 block font-bold">Rp. {{ $value->biaya }}</span>
                                @if($value->diskon != 0)
                                    <span class="text-xs block text-red-500 font-bold line-through">Rp. {{ $value->diskon }}</span>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="block w-max type-produk rounded-lg mt-1 font-bold p-1 bg-green-500 text-slate-50 text-[0.6em]">{{ $value->produk->type_produk }}</span>
                                    @if($value->order->status_order == 0)
                                        <span class="block w-max rounded-lg mt-1 font-bold p-1 bg-red-500 text-slate-50 text-[0.6em]">Belum Bayar</span>
                                    @elseif($value->order->status_order == 'PENDING')
                                        <span class="block w-max rounded-lg mt-1 font-bold p-1 bg-orange-500 text-slate-50 text-[0.6em]">Pending</span>
                                    @elseif($value->order->status_order == 'SUCCESS')
                                        <span class="block w-max rounded-lg mt-1 font-bold p-1 bg-green-500 text-slate-50 text-[0.6em]">Sukses</span>
                                    @elseif($value->order->status_order == 'CANCEL')
                                        <span class="block w-max rounded-lg mt-1 font-bold p-1 bg-red-500 text-slate-50 text-[0.6em]">Cancel</span>
                                    @endif
                                </div>
                                <a href="{{ route('user.pesanan', getUserName()).'?no_order='.$value->no_order.'&vx='.$value->id }}" class="px-3 py-2 text-[0.6em] absolute right-0 bottom-0 text-slate-50 rounded-md bg-blue-500">Detail</a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div style="display: none" id="tidakAdaProduk" class="w-full">
                <div class="w-full p-4 rounded-lg bg-slate-300 text-center">
                    <span class="text-slate-500 text-sm">Tidak Ada Produk</span>
                </div>
            </div>
        @else
            <div class="w-full">
                <div class="flex flex-col justify-center items-center p-6 h-full rounded-lg">
                    <img src="{{ asset('assets/frontend/images/no-cart.svg') }}" class="h-[300px]" alt="Transaksi Anda Masih Kosong">
                    <div class="py-3">
                        <span class="font-bold lg:text-lg text-sm">Pesanan Kamu Masih Kosong</span>
                    </div>
                    <a href="{{ route('home') }}" class="px-6 text-center block py-2 bg-blue-600 text-slate-50 rounded-md">Lihat Produk</a>
                </div>
            </div>
        @endif
    </div>
</div>

{!! renderScript('script-pesanan') !!}