@php
    $user = Auth::user();
@endphp
<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>

    @if(@count($wishlist) > 0)
        <ul class="mt-3">
            @foreach($wishlist as $key => $value)
                <li class="flex mb-3 border hover:-translate-y-1 transition-all hover:cursor-pointer hover:shadow-md flex-col rounded-lg relative">
                    <div class="flex gap-1 w-full items-start p-3">
                        <div>
                            <div style="background-image: url({{ $value->image_produk[0]->url ?? config('app.logo') }})" class="min-w-[80px] bg-cover bg-center min-h-[80px] bg-slate-400">

                            </div>
                        </div>
                        <div class="px-1 lg:w-full text-[0.9em] relative">
                            <h4 class="font-bold text-[0.9em] mb-2">{{ $value->nama_produk }}</h4>
                            <div class="flex gap-2">
                                <span class="bg-blue-400 block w-max mb-3 text-[0.6em] px-2 py-1 rounded-lg text-slate-50">{{ $value->nama_toko }}</span> |
                                <span class="font-bold">Toko Baru</span>
                            </div>
                            <p class="font-bold text-blue-600">Rp. {{ $value->harga['harga_fixed'] }}</p>
                            <p class="text-slate-400 text-[0.6em] line-through">Rp. {{ $value->harga['harga_real'] }}</p>
                            <div class="absolute right-0 bottom-0 w-max text-[2em] pr-3 text-blue-600">
                                <button class="bg-blue-600 rounded text-slate-50 block px-3 py-2 text-xs">
                                    Masukkan Ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="rounded-lg mt-4 px-2 py-3 bg-white">
            <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="px-3 text-center py-2 bg-blue-600 rounded-md text-slate-50 w-full">
                <i class="ri-search-line me-2"></i> Cari Produk
            </a>
            </div>
        </div>
    @else
        <div class="w-full h-[400px] overflow-hidden">
            <div class="flex flex-col justify-center items-center p-6 h-full rounded-lg">
            <img src="{{ asset('assets/frontend/images/no-cart.svg') }}" class="h-[200px]" alt="Keranjang Anda Masih Kosong">
            <div class="py-3 text-center">
                <span class="font-bold text-lg">WishList Masih Kosong</span>
            </div>
            <a href="{{ route('home') }}" class="px-6 text-center block py-2 bg-blue-600 text-slate-50 rounded-md">Lihat Produk</a>
            </div>
        </div>
    @endif
</div>