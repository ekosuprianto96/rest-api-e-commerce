@extends('frontend.layouts.index', ['title' => $produk->nm_produk])
<style>
  
  .owl-stage {
        display: flex;
        align-items: center
    }
</style>
@section('content')
<x-frontend.layouts.container>
  <div>
    <div class="mb-3 rounded-lg shadow-lg gap-3 grid grid-cols-1 lg:grid-cols-2 w-full bg-white overflow-hidden lg:p-8 lg:mt-8 mt-4 lg:py-8">
      <div class="overflow-hidden">
        @if(@count($produk->images) > 0)
          <div id="default-carousel" class="relative w-full" data-carousel="slide">
            <!-- Carousel wrapper -->
            <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
                <!-- Item 1 -->
                @foreach($produk->images as $key => $value)
                  <div data-carousel-item style="background-image: url({{ $value->url }})" class="min-w-full hidden border duration-700 ease-in-out rounded-lg bg-center bg-cover h-[400px] bg-slate-400 max-h-[400px]"></div>
                @endforeach
            </div>
            <!-- Slider indicators -->
            <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                @foreach($produk->images as $key => $value)
                  <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="" data-carousel-slide-to="{{ $key }}"></button>
                @endforeach
            </div>
            <!-- Slider controls -->
            <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            </button>
            <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            </button>
          </div>
        @else
          <div style="background-image: url({{ getSettings('logo') }})" class="w-full rounded-lg bg-center bg-cover h-[400px] bg-slate-400 max-h-[400px]"></div>
        @endif
      </div>
      <div class="px-2 relative">
        <h1 class="font-bold text-[1.2em] lg:text-[1.5em] lg:mb-4">{{ $produk->nm_produk }}</h1>
        <p class="font-bold text-[1.3em] lg:text-[1.8em] text-blue-600">Rp. {{ $produk->detail_harga['harga_fixed'] }}</p>
        <p class="text-[1em] line-through">Rp. {{ $produk->harga }}</p>
        @if($produk->potongan)
          <span class="block rounded-lg w-max px-2 bg-red-400 text-white text-xs">Disk : Rp. {{ $produk->potongan }}</span>
        @endif
        <div class="text-[1.2em] py-2 flex lg:py-2 items-center gap-2">
          <i class="ri-star-fill"></i>
          <i class="ri-star-fill"></i>
          <i class="ri-star-fill"></i>
          <i class="ri-star-fill"></i>
          <i class="ri-star-fill"></i>
        </div>
        <div class="text-[1.2em] py-2 flex items-center gap-3">
          <span class="bg-blue-400 text-[0.6em] px-2 py-1 rounded-lg text-slate-50">{{ $produk->kategori->nama_kategori }}</span> |
          <span class="text-[0.8em]">Terjual {{ $produk->total_terjual }}</span>
        </div>
        @if(intval($produk->status_referal))
          <span class="bg-green-200 px-3 py-1 text-green-600 rounded-lg text-sm">
              <i class="ri-percent-fill text-green-600"></i> Komisi Affiliasi Rp. {{ $produk->komisi_referal }}
          </span>
        @endif
        <div class="py-2 lg:w-full">
          <span class="text-sm flex mb-2">Link Afiliasi 
              <button class="ms-1" data-popover-target="infoAffiliasi" type="button"><i class="ri-information-fill hover:cursor-pointer hover:text-blue-600"></i></button>
              <div data-popover id="infoAffiliasi" role="tooltip" class="absolute z-[90] invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
                  <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                      <h3 class="font-semibold text-gray-900 dark:text-white">Program Afiliasi</h3>
                  </div>
                  <div class="px-3 py-2">
                      <p>Jika anda membagikan link Affiliate ini, maka anda akan mendapatkan komisi Affiliate ketika ada member yang membeli produk ini menggunakan link Affiliate yang anda bagikan</p>
                  </div>
                  <div data-popper-arrow></div>
              </div>
          </span>
          <div class="w-full flex items-center rounded relative border overflow-hidden h-max">
            <input ref="linkReferal" type="text" readonly="true" class="w-full bg-slate-200 focus:outline-none bg-none px-4 py-2 lg:text-sm text-[0.6em]" value="link_referal">
            <button class="absolute right-0 text-slate-50 bg-blue-600 p-4 text-sm">Copy Code</button>
          </div>
        </div>
        <div class="py-2 flex flex-wrap gap-2 text-blue-500">
          @if(isset($produk->garansi)) 
            <button>
              <i class="ri-gift-fill"></i>
              Garansi {{ $produk->garansi }} Hari
            </button>
          @endif
          <button>
            <i class="ri-alarm-fill"></i>
            Waktu Proses {{ ($produk->waktu_proses) }} 
          </button>
        </div>
        <div class="lg:py-4 py-2 mb-3">
          @auth
            <button id="buttonAddCart" data-produk="{{ $produk->kode_produk }}" class="px-3 py-3 hover:bg-blue-400 bg-blue-600 rounded-md w-full text-sm text-slate-50">Masukkan Ke Keranjang</button>
          @else
            <a href="{{ route('login') }}" class="px-3 py-3 block text-center hover:bg-blue-400 bg-blue-600 rounded-md w-full text-sm text-slate-50">Masukkan Ke Keranjang</a>
          @endauth
        </div>
      </div>
    </div>
    @if(count($produk->form) > 0)
      <div class="border p-2 mb-3 bg-white rounded-lg">
        <div class="w-full lg:p-4 p-1">
          <div class="mb-4">
            <h3 class="font-bold lg:text-[1.1em] text-[1em]">Data Yang Diperlukan</h3>
          </div>
          <div class="py-4">
              @foreach($produk->form as $key => $value)
                  <div class="relative mb-2 overflow-hidden w-full">
                      <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">{{ $value->label }}</label>
                      <input type="{{ $value->type }}" class="w-full border data_form_produk rounded-lg focus:border-red-500 hover:bg-slate-200 h-[65px] p-4 pt-6 focus:outline-none text-sm" placeholder="Masukkan data...">
                  </div>
              @endforeach
          </div>
          <div class="flex items-center justify-end">
            <button class="px-6 py-2 text-slate-50 rounded-md bg-green-500">Kirim</button>
          </div>
        </div>
      </div>
    @endif
    <div class="overflow-hidden lg:hidden">
      <div class="rounded-lg mb-3 overflow-hidden relative bg-white p-2">
          <button type="button" class="flex w-full justify-between p-2 relative z-30 hover:bg-blue-300 rounded-lg hover:text-slate-50">
              <h3 class="font-bold">Deskripsi Produk</h3>
              <span class="text-blue-600">
                <i class="ri-arrow-down-s-line"></i>
              </span>
          </button>
          <div class="overflow-hidden px-2 z-10" style="transition: height 0.5s;">
              {!! nl2br($produk->deskripsi) !!}
          </div>
      </div>
    </div>
    <div class="mb-3 rounded-2xl shadow-lg relative p-3 py-4 flex items-center gap-4 bg-blue-300">
        <div class="w-[30%] lg:w-[15%] flex justify-center items-center">
            <div style="background-image: url({{ $produk->toko->image }})" class="lg:w-[80px] bg-cover bg-center lg:h-[80px] w-[60px] h-[60px] bg-slate-50 rounded-full"></div>
        </div>
        <div class="w-[80%] lg:w-[85%] h-full text-sm">
            <h3 class="font-bold text-[1em] mb-2">{{ $produk->toko->nama_toko }}</h3>
            <p class="mb-2">Produk : {{ $produk->total_produk_toko }} | Terjual : {{ $produk->total_terjual_toko }}</p>
            <div class="w-full py-3 flex items-center gap-3">
                <a href="" class="px-2 py-1 bg-blue-600 text-slate-50 text-[0.8em] rounded-md whitespace-nowrap"><i class="ri-store-3-fill lg:me-2"></i> Lihat Profile</a>
                <a href="" class="px-2 py-1 bg-green-600 text-[0.8em] rounded-md text-slate-50 whitespace-nowrap"><i class="ri-question-answer-fill lg:me-2"></i> Chat Penjual</a>
            </div>
        </div>
        <span class="absolute top-4 right-4 bg-green-500 text-xs text-slate-50 rounded-full px-2">Online</span>
    </div>
    <div class="shadow-lg p-8 hidden lg:block bg-white rounded-lg">
        <div class="w-full p-4">
            <div class="mb-4">
                <h3 class="font-bold lg:text-[1.1em] text-[1em]">Deskripsi Produk</h3>
            </div>
            <div class="py-4" ref="deskripsiProdukMobile">
                {!! nl2br($produk->deskripsi) !!}
            </div>
        </div>
    </div>
  </div>
  <div class="mt-8">
    <div class="mb-4">
        <h3 class="font-bold lg:text-[1.3em] text-[1em]">Produk Lain Dari Toko Ini</h3>
    </div>
    <div class="flex mb-4 produk-toko lg:overflow-hidden">
        @foreach($produkToko as $key => $value)
            <x-frontend.produk.card-produk :produk="$value"  />
        @endforeach
    </div>

    <div class="mb-4">
      <h3 class="font-bold lg:text-[1.3em] text-[1em]">Produk Serupa</h3>
    </div>
    @if(@count($produkSerupa) > 0)
      <div class="flex mb-3 produk-serupa card-produk lg:overflow-hidden">
          @foreach($produkSerupa as $key => $value)
              <x-frontend.produk.card-produk :produk="$value"  />
          @endforeach
      </div>
    @else
      <div class="w-full border mb-4 hover:bg-slate-300 bg-white py-8 rounded-lg flex justify-center items-center">
        <span>Tidak Ada Produk</span>
      </div>
    @endif
  </div>
</x-frontend.layouts.container>

@include('frontend.layouts.script-produk-show')
@endsection
