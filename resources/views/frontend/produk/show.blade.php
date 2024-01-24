@extends('frontend.layouts.index', ['title' => $produk->nm_produk])

@section('content')
    <x-frontend.layouts.container>
        <div class="detail-produk">
            <div class="mb-3 rounded-lg shadow-lg gap-3 grid grid-cols-1 lg:grid-cols-2 w-full bg-white overflow-hidden rounded lg:p-8 lg:mt-8 mt-4 lg:py-8">
                <div v-if="produk && !skeleton">
                    <div style="background-image: url({{ @count($produk->images) > 0 ? $produk->images[0]->url : getSettings('logo') }})" class="w-full rounded-lg bg-center bg-cover h-[400px] bg-slate-400 max-h-[400px]">
                      
                    </div>
                </div>
                <div class="px-2 relative">
                  <h1 class="font-bold text-[1.2em] lg:text-[1.5em] lg:mb-4">{{ $produk->nm_produk }}</h1>
                  <p class="font-bold text-[1.3em] lg:text-[1.8em] text-blue-600" v-if="produk">Rp. {{ $produk->detail_harga['harga_fixed'] }}</p>
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
                  <div v-if="parseInt(produk.status_referal) > 0" class="py-2 lg:w-full">
                    <span class="text-sm flex mb-2">Link Afiliasi 
                        <button><i class="ri-information-fill hover:cursor-pointer hover:text-blue-600"></i></button>
                    </span>
                    <div class="w-full flex items-center rounded relative border overflow-hidden h-max">
                      <input ref="linkReferal" type="text" readonly="true" class="w-full bg-slate-200 focus:outline-none bg-none px-4 py-2 lg:text-sm text-[0.6em]" value="link_referal">
                      <button class="absolute right-0 text-slate-50 bg-blue-600 p-4 text-sm">Copy Code</button>
                    </div>
                  </div>
                  <div v-if="produk.garansi > 0" class="py-2 flex flex-wrap gap-2 text-blue-500">
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
                    <button class="px-3 py-2 bg-blue-600 rounded-md w-full text-sm text-slate-50">Masukkan Ke Keranjang</button>
                  </div>
                </div>
              </div>
              @if(count($produk->form) > 0)
                <div v-if="produk && produk.type_produk == 'MANUAL' && produk.form.length > 0 && !produk.status_form" class="border p-2 mb-3 bg-white rounded-lg">
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
                      <div style="background-image: url({{ $produk->toko->image }})" class="lg:w-[80px] bg-cover bg-center lg:h-[80px] w-[60px] h-[60px] bg-slate-50 rounded-full">
              
                      </div>
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
              <div class="py-4 w-full">
                  <div class="mb-4">
                      <h3 class="font-bold lg:text-[1.3em] text-[1em]">Produk Lain Dari Toko Ini</h3>
                  </div>
                  <div class="flex lg:overflow-hidden">
                      @foreach($produkToko as $key => $value)
                          <x-frontend.produk.card-produk :produk="$value"  />
                      @endforeach
                  </div>
              </div>
              <div class="py-4 w-full">
                  <div class="mb-4">
                      <h3 class="font-bold lg:text-[1.3em] text-[1em]">Produk Serupa</h3>
                  </div>
                  <div class="flex lg:overflow-hidden">
                      @foreach($produkSerupa as $key => $value)
                          <x-frontend.produk.card-produk :produk="$value"  />
                      @endforeach
                  </div>
              </div>
        </div>
    </x-frontend.layouts.container>
@endsection

@include('frontend.layouts.script-produk-show')