@extends('frontend.layouts.index', ['title' => 'Belanja Produk Digital Murah'])

@section('content')
    <x-frontend.banners></x-frontend.banners>
    <x-frontend.layouts.container>
        <div class="my-4">
            <h3 class="font-bold lg:text-[1.3em] text-[1em]">Produk Terbaru</h3>
        </div>
        <div class="flex lg:overflow-hidden">
            @foreach($produk as $key => $value)
                <x-frontend.produk.card-produk :produk="$value"  />
            @endforeach
        </div>

        <div class="my-4">
            <h3 class="font-bold lg:text-[1.3em] text-[1em]">Produk Terlaris</h3>
        </div>
        <div class="flex lg:overflow-hidden">
            @foreach($produkTerlaris as $key => $value)
                <x-frontend.produk.card-produk :produk="$value"  />
            @endforeach
        </div>

        <div class="my-4">
            <h3 class="font-bold lg:text-[1.3em] text-[1em]">Semua Produk</h3>
        </div>
        <div class="flex lg:overflow-hidden">
            @foreach($semuaProduk as $key => $value)
                <x-frontend.produk.card-produk :produk="$value"  />
            @endforeach
        </div>
        <div class="py-8 text-center">
            <a href="" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-slate-50 rounded-md">Tampilkan Semua Produk</a>
        </div>
    </x-frontend.layouts.container>
@endsection