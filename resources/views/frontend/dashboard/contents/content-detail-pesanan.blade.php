
<div class="py-3">
    <div class="lg:h-[600px] lg:p-4 overflow-y-auto">
        <div class="text-end w-full mt-2">
            <a href="{{ route('user.pesanan', getUserName()) }}" class="px-3 py-2 text-slate-50 text-sm bg-blue-500 rounded-lg">Kembali</a>
        </div>
        <div class="mt-4">
            <div id="detailOrder" class="flex mb-3 py-3 rounded-lg hover:px-2 transition-all hover:cursor-pointer justify-between items-center hover:bg-blue-300 hover:text-slate-50">
                <h4 class="text-sm font-bold">Detail Order</h4>
                <i class="ri-arrow-down-s-line transition-all rotate-180" style="line-height: normal;"></i>
            </div>
            <hr class="border-dotted border-b-4 border-t-0">
            @if($pesanan->produk->type_produk == 'MANUAL')
                <div class="py-4 px-4">
                    <div class="relative flex items-center w-full justify-between">
                        <span class="{{ $pesanan->status_order == 'CANCEL' ? 'bg-red-500' : 'bg-green-500' }} w-[18px] z-40 h-[18px] flex items-center justify-center rounded-full relative">
                        <i class="ri-checkbox-circle-fill text-slate-50 text-xs"></i>
                        <span class="absolute top-[100%] text-xs">Pending</span>
                        </span>
                        <span class="w-[18px] {{ ($pesanan->status_order == 'PENDING' || $pesanan->status_order == 'CANCEL') ? 'bg-red-500' : ($pesanan->status_order == 'PROCCESS' ? 'bg-green-500' : 'bg-green-500') }} z-40 h-[18px] flex items-center justify-center rounded-full relative">
                        @if($pesanan->status_order == 'PROCCESS' || $pesanan->status_order == 'SUCCESS')
                            <i class="ri-checkbox-circle-fill text-slate-50 text-xs"></i>
                        @else
                            <i class="ri-close-circle-fill text-slate-50 text-xs"></i>
                        @endif
                        <span class="absolute top-[100%] text-xs">Proccess</span>
                        </span>
                        <span class="w-[18px] {{ ($pesanan->status_order == 'SUCCESS' ? 'bg-green-500' : 'bg-red-500') }} z-40 h-[18px] flex items-center justify-center rounded-full relative">
                        @if($pesanan->status_order == 'SUCCESS')
                            <i class="ri-checkbox-circle-fill text-slate-50 text-xs"></i>
                        @else
                            <i class="ri-close-circle-fill text-slate-50 text-xs"></i>
                        @endif
                        <span class="absolute top-[100%] text-xs">Success</span>
                        </span>
                        <div class="w-[100%] h-[3px] z-30 absolute left-0 right-0 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            @endif
            <div id="conntentDetailOrder" class="border active rounded-lg p-2 mt-4">
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-2 text-sm">No Order</td>
                            <td class="text-end py-2 text-sm">{{ $pesanan->no_order }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Tanggal</td>
                            <td class="text-end py-2 text-sm">{{ $pesanan->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Diskon</td>
                            <td class="text-end py-2 text-sm">Rp. {{ number_format($pesanan->potongan_diskon, 0, 0, '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Total Dibayar</td>
                            <td class="text-end py-2 text-sm">Rp. {{ number_format($pesanan->total_biaya, 0, 0, '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Status Pembayaran</td>
                            <td class="text-end py-2">
                                <div class="w-full flex justify-end items-center">
                                    @if($pesanan->order->status_order == 0)
                                        <span class="block font-bold p-1 text-red-500 text-sm">Belum Dibayar</span>
                                    @elseif($pesanan->order->status_order == 'SUCCESS')
                                        <span class="block font-bold p-1 text-green-500 text-sm">Sukses</span>
                                    @elseif($pesanan->order->status_order == 'PENDING')
                                        <span class="block font-bold p-1 text-orange-500 text-sm">Pending</span>
                                    @elseif($pesanan->order->status_order == 'CANCEL')
                                        <span class="block font-bold p-1 text-red-500 text-sm">Cancel</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if($pesanan->produk->type_produk === 'MANUAL')
                            <tr>
                                <td class="py-2 text-sm">Status Order</td>
                                <td class="text-end py-2">
                                    <div class="w-full flex justify-end items-center">
                                        @if($pesanan->status_order == 0)
                                            <span class="block font-bold p-1 text-red-500 text-sm">Pending</span>
                                        @elseif($pesanan->status_order == 'SUCCESS')
                                            <span class="block font-bold p-1 text-green-500 text-sm">Sukses</span>
                                        @elseif($pesanan->status_order == 'PENDING')
                                            <span class="block font-bold p-1 text-orange-500 text-sm">Pending</span>
                                        @elseif($pesanan->status_order == 'CANCEL')
                                            <span class="block font-bold p-1 text-red-500 text-sm">Cancel</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="py-2 text-sm">Metode Pembayaran</td>
                            <td class="text-end py-2 text-sm">
                                @if($pesanan->order->type_payment == 'manual')
                                    <span class="text-blue-500 text-sm">(Bank Transfer)</span>
                                @endif
                                {{ isset($pesanan->order->payment) ? $pesanan->order->payment->payment_name : ($pesanan->order->type_payment == 'gateway' ? 'Gateway' : 'Lingga Pay') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="py-2 flex justify-end">
                    @if($pesanan->status_order == 'PENDING' || $pesanan->status_order == '0')
                        <a href="{{ route('user.pesanan', getUserName()) }}" class="px-3 py-2 text-slate-50 text-sm bg-red-500 rounded-lg">Cancel</a>
                    @endif
                </div>
            </div>
        </div>
        <div class=" mt-4">
            <div id="detailProduk" class="flex mb-3 py-3 rounded-lg hover:px-2 transition-all hover:cursor-pointer justify-between items-center hover:bg-blue-300 hover:text-slate-50">
                <h4 class="text-sm font-bold">Detail Produk</h4>
                <i class="ri-arrow-down-s-line transition-all" style="line-height: normal;"></i>
            </div>
            <hr class="border-dotted border-b-4 border-t-0">
            <div id="conntentDetailProduk" class="border rounded-lg p-2 mt-4" style="display: none">
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-2 text-sm">Nama Produk</td>
                            <td class="text-end py-2 text-sm">{{ $pesanan->produk->nm_produk }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Kategori</td>
                            <td class="text-end py-2 text-sm">{{ $pesanan->produk->kategori->nama_kategori }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Nama Penjual</td>
                            <td class="text-end py-2 text-sm">{{ $pesanan->produk->toko->nama_toko }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Harga</td>
                            <td class="text-end py-2 text-sm">Rp. {{ number_format($pesanan->produk->harga, 0, 0, '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Total Diskon</td>
                            <td class="text-end py-2 text-sm">Rp. {{ $pesanan->produk->getHargaDiskon()['harga_diskon'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Harga Fixed</td>
                            <td class="text-end py-2 text-sm">Rp. {{ number_format($pesanan->produk->getHargaFixed(), 0, 0, '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-sm">Type Produk</td>
                            <td class="text-end py-2 text-sm">{{ $pesanan->produk->type_produk }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="py-2 flex justify-end">
                    <a href="{{ route('produk.show', $pesanan->produk->slug) }}" class="px-3 py-2 text-slate-50 text-sm bg-blue-500 rounded-lg">Lihat Produk</a>
                </div>
            </div>
            <div class="border rounded-lg p-2 mt-4">
                <ul class="w-full">
                    <li class="py-2 text-sm flex justify-between">
                        <span>Waktu Proses</span>
                        <span>1 Hari</span>
                    </li>
                    <li class="py-2 text-sm flex justify-between">
                        <span>File</span>
                        <a href="{{ route('user.pesanan', getUserName()) }}" class="px-3 py-1 text-slate-50 text-sm bg-blue-500 rounded-lg">Download</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){      
                    
        $('#detailProduk').click(function(event) {
            if($('#conntentDetailProduk').hasClass('active')) {
                $('#conntentDetailProduk').hide().removeClass('active');
                $(this).find('i').removeClass('rotate-180');
                return;
            }

            $('#conntentDetailProduk').show().addClass('active');
            $(this).find('i').addClass('rotate-180');
            return;
        })

        $('#detailOrder').click(function(event) {
            if($('#conntentDetailOrder').hasClass('active')) {
                $('#conntentDetailOrder').hide().removeClass('active');
                $(this).find('i').removeClass('rotate-180');
                return;
            }

            $('#conntentDetailOrder').show().addClass('active');
            $(this).find('i').addClass('rotate-180');
            return;
        })
          
    });     
    
</script>