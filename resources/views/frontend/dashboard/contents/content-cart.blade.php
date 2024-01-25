@php
    $user = Auth::user();
@endphp
<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>

    @if(@count($carts) > 0)
        <ul class="mt-4">
            @foreach($carts as $key => $value)
                <li class="flex mb-2 border-b-2 pl-4 relative">
                    <div class="absolute -left-[2px] top-0">
                        <input type="checkbox" checked>
                    </div>
                    <div class="flex gap-1 w-full items-start">
                        <div>
                            <div style="background-image: url({{ $value->images[0]->url ?? config('app.logo') }})" class="min-w-[80px] bg-cover bg-center min-h-[80px] bg-slate-400">
            
                            </div>
                        </div>
                        <div class="px-1 lg:w-full text-[0.9em]">
                            <h4 class="font-bold text-[0.9em] mb-2">{{ $value->nm_produk }}</h4>
                            <div class="flex gap-2">
                                <span class="bg-blue-400 block w-max mb-3 text-[0.6em] px-2 py-1 rounded-lg text-slate-50">{{ $value->nama_kategori }}</span> |
                                <span class="font-bold">{{ $value->nama_toko }}</span>
                            </div>
                            <p class="font-bold text-blue-600">Rp. {{ $value->harga['harga_fixed'] }}</p>
                            <p class="text-slate-400 text-[0.6em] line-through">Rp. {{ $value->harga['harga_real'] }}</p>
                            <div class="py-2 text-[1.3em] w-full flex items-center justify-end text-blue-600 gap-2">
                            <button type="button">
                                <i class="ri-delete-bin-5-fill"></i>
                            </button>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="p-3 mt-3 bg-white rounded-lg">
            <h4 class="text-sm">Pilih Metode Pembayaran</h4>
            <ul class="mt-2">
              <li class="border relative flex items-center mb-2 p-3 rounded-lg hover:cursor-pointer hover:bg-slate-100 hover:shadow-lg">
                <input type="checkbox" class="me-2">
                <label for="" class="font-bold hover:cursor-pointer">LinggaPay</label>
                <span class="text-danger text-sm absolute right-4">Saldo : Rp. {{ $user->iorpay->saldo }}</span>
              </li>
              <li class="border flex flex-col mb-2 p-3 rounded-lg hover:cursor-pointer hover:bg-slate-100 hover:shadow-lg">
                <div>
                  <input type="checkbox" class="me-2">
                  <label for="" class="font-bold hover:cursor-pointer">Manual Transfer</label>
                </div>
                {{-- <ul v-if="show_modal" class="w-full mt-3 pl-3">
                  <li @click="pilihBank(pay.kode_payment)" v-for="(pay, index) in payment" :key="index" :class="pay.kode_payment == bank_id ? 'shadow-lg bg-blue-400 text-slate-50' : ''" class="border relative flex items-center hover:cursor-pointer hover:shadow-lg hover:bg-blue-400 hover:text-slate-50 p-3 mb-2 rounded-lg">
                    <img width="30" :src="pay.image" :alt="pay.payment_name">
                    <span class="block ms-2 font-bold">{{ pay.payment_name.toUpperCase() }}</span>
                    <input class="absolute right-6" type="radio" :checked="pay.kode_payment == bank_id">
                  </li>
                </ul> --}}
              </li>
              @if(getSettingsGateway('midtrans', 'status_gateway') == 1)
                <li class="border flex items-center mb-2 p-3 rounded-lg hover:cursor-pointer hover:bg-slate-100 hover:shadow-lg">
                    <input :checked="paymentMethod == 'gateway'" type="checkbox" class="me-2">
                    <label class="font-bold hover:cursor-pointer" for="">Payment Gateway</label>
                </li>
              @endif
            </ul>
        </div>
    
        <div v-if="carts.length > 0" class="rounded-lg mt-4 px-2 py-3 bg-white">
            <div class="flex items-center gap-2">
              <button class="px-3 py-2 border-blue-600 rounded-md text-slate-400 border w-1/2">Pilih Semua</button>
              <button v-if="!is_loading" type="button" class="px-3 text-center py-2 bg-blue-600 rounded-md text-slate-50 w-1/2">Checkout</button>
              {{-- <button v-else type="button" class="px-3 text-center py-2 bg-blue-600 rounded-md text-slate-50 w-1/2">
                <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                  <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                </svg>
                Loading
              </button> --}}
            </div>
        </div>
    @else
        <div class="w-full h-[400px] overflow-hidden">
            <div class="flex flex-col justify-center items-center p-6 h-full rounded-lg">
            <img src="{{ asset('assets/frontend/images/no-cart.svg') }}" class="h-[200px]" alt="Keranjang Anda Masih Kosong">
            <div class="py-3 text-center">
                <span class="font-bold text-lg">Keranjang Anda Masih Kosong</span>
            </div>
            <a href="{{ route('home') }}" class="px-6 text-center block py-2 bg-blue-600 text-slate-50 rounded-md">Lihat Produk</a>
            </div>
        </div>
    @endif
</div>