<div class="bg-green-200 rounded-md my-2 p-3">
    <span class="block font-bold mb-2 text-sm">Perhatian :</span>
    <p class="text-[0.7em]">Untuk pembayaran komisi, komisi langsung masuk ke saldo LinggaPay dan bisa langsung di withdraw dengan minimum withdraw Rp. 10.000</p>
  </div>
  <div class="py-2">
    {!! $chart->container() !!}
  </div>
  <div class="grid gap-2 grid-cols-2 mb-3">
    <div class="p-2 border rounded-md hover:cursor-pointer">
      <span class="text-sm font-semibold block mb-2">Total Produk</span>
      <span class="text-[1.1em] font-semibold block">{{ $komisi['total']['total_produk'] }}</span>
    </div>
    <div class="p-2 border rounded-md">
      <span class="text-sm font-semibold mb-2 block">Total Komisi</span>
      <span class="text-[1.2em] font-semibold block">Rp. {{  $komisi['total']['total_komisi'] }}</span>
    </div>
  </div>
  @if(@count($komisi['data']) > 0)
    <div class="w-full max-h-[120vh] overflow-y-auto">
        <ul class="w-full h-full">
            @foreach($komisi['data'] as $key => $value)
                <li class="border flex items-center shadow-md mb-2 rounded-lg relative overflow-hidden p-2">
                    <div class="w-[30%] lg:w-max">
                        <img src="{{ $value['image'] }}" class="w-full  lg:w-[100px]" alt="{{ $value['nama_produk'] }}">
                    </div>
                    <div class="flex w-[70%] flex-col pl-4 relative overflow-hidden">
                        <span class="font-semibold my-1 text-sm bg-blue-500 rounded-lg w-max px-2 text-slate-50">{{ $value['nama_toko'] }}</span>
                        <span class="font-bold text-sm my-1 truncate">{{ $value['nama_produk'] }}</span>
                        <span class="font-lighter text-xs">Harga Produk : Rp. {{ $value['detail_harga']['harga_fixed'] }}</span>
                        <span class="font-lighter text-xs">Komisi : <span class="text-green-500">+Rp. {{ $value['total_komisi'] }}</span></span>
                        <span class="font-lighter text-xs">Status Pembayaran : <span class="text-green-500">{{ $value['status'] }}</span></span>
                        <span class="text-slate-400 text-xs">{{ $value['tanggal'] }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
  @else
    <div class="flex justify-center p-8 items-center flex-col h-[400px]">
        <img class="w-[200px] mb-4" src="{{ asset('assets/frontend/images/tidak-ada-komisi.svg') }}" alt="Tidak Ada Komisi">
        <span class="text-md text-center">Kamu belum memiliki komisi, yuk! ikut cari cuan dengan membagikan link produk Affiliate.</span>
    </div>
  @endif