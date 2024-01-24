<div class="relative z-20 min-w-[70%] lg:min-w-[20%] p-1 rounded-lg">
    <span class="absolute z-50 top-4 right-4  ">
        <button class="{{ $produk->wishlist == 1 ? 'text-red-500' : 'hover:text-red-500' }}">
            <i class="ri-heart-fill"></i>
        </button>
    </span>
    <div class="bg-slate-400 border flex flex-col justify-between border-slate-200 shadow-lg rounded-lg overflow-hidden min-w-full min-h-max max-h-max">
        <div style="background-image: url({{ @count($produk->images) <= 0 ? config('app.logo') : $produk->images[0]->url }})" class="min-w-full relative bg-center bg-cover h-[200px] border flex justify-center items-center overflow-hidden">
            <span class="text-white px-2 py-1 shadow-lg text-xs rounded-lg bg-blue-600 absolute bottom-2 left-2">{{ $produk->toko->nama_toko }}</span>
        </div>
        <div class="p-2 bg-slate-50 min-h-[150px] w-full overflow-hidden text-start">
            <a href="{{ route('produk.show', $produk->slug) }}" class="hover:text-blue-500">
                <h4 class="font-bold text-[0.9em] truncate hover:overflow-visible hover:whitespace-normal">{{ $produk->nm_produk }}</h4>
            </a>
            <p class="text-[0.8em]">{{ $produk->kategori->nama_kategori }}</p>
            <div class="text-[0.8em]">
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
            </div>
            @if($produk->potongan)
                <span class="block my-2 rounded-lg w-max px-2 bg-red-400 text-white text-xs">Disk : Rp. {{ $produk->potongan }}</span>
            @endif
            <p class="text-[0.9em] font-bold">Rp. {{ $produk->detail_harga['harga_fixed'] }}</p>
            @if($produk->potongan)
                <p class="text-[0.8em] line-through">Rp. {{ $produk->harga }}</p>
            @endif
        </div>
    </div>
</div>
