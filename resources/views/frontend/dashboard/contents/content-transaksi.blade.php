@php
    $user = Auth::user();
@endphp
<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>

    @if(@count($transaksi) > 0)
        <ul class="overflow-y-auto shadow-inner pt-2 mt-4 max-h-[600px]">
            @foreach($transaksi as $key => $value)
                <li class="p-4 border mb-3 rounded-lg list-transaksi">
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <span class="text-sm flex items-center">
                                @if($value->status_order == '0')
                                    <span class="bg-red-500 text-[0.8em] px-3 rounded-lg text-slate-50">Belum Dikonfirmasi</span>
                                @elseif($value->status_order == 'SUCCESS')
                                    <span class="bg-green-500 text-[0.8em] px-3 rounded-lg text-slate-50">Success</span>
                                @elseif($value->status_order == 'PENDING')
                                    <span class="bg-yellow-500 text-[0.8em] px-3 rounded-lg text-slate-50">Pending</span>
                                @elseif($value->status_order == 'CANCEL')
                                    <span class="bg-red-500 text-[0.8em] px-3 rounded-lg text-slate-50">Cancel</span>
                                @endif
                            </span>
                            <span class="text-xs text-slate-400 block mt-2">{{ $value->tanggal }}</span>
                            <div class="flex flex-col">
                                <span class="text-sm">Total: Rp. {{ $value->total_biaya }}</span>
                                <span class="text-blue-500 text-sm">
                                {{ $value->type_payment == 'manual' ? 'Bank Transfer' : ($value->type_payment == 'linggaPay' ? 'Lingga Pay' : 'Payment Gateway') }}
                                </span>
                            </div>
                        </div>
                        <button id="buttonArrowTransaksi_{{ $key }}" type="button" class="w-[45px] arrow-transaksi h-[45px] bg-slate-100 text-slate-300 hover:bg-slate-300 hover:text-slate-400 transition-all rounded-full flex justify-center items-center text-[1.8em]">
                            <i class="ri-arrow-down-s-line transition-all" style="line-height: normal;"></i>
                        </button>
                    </div>
                    <div id="detailTransaksi_{{ $key }}" style="display: none" class="w-full detail-transaksi border-t-2 py-2 mt-3">
                        <ul class="w-full">
                        <li class="flex text-sm py-1 justify-between items-center">
                            <span>No Transaksi</span>
                            <span>{{ $value->no_order }}</span>
                        </li>
                        <li class="flex text-sm py-1 justify-between items-center">
                            <span>Tanggal Transaksi</span>
                            <span>{{ $value->tanggal }}</span>
                        </li>
                        <li class="flex text-sm py-1 justify-between items-center">
                            <span>Total Transaksi</span>
                            <span>Rp. {{ $value->total_biaya }}</span>
                        </li>
                        @if($value->type_payment == 'manual')
                            <li v-if="order.type_payment == 'manual'" class="flex text-sm py-1 justify-between items-center">
                                <span>Bank Tujuan</span>
                                <span>{{ $value->payment->payment_name }}</span>
                            </li>
                            <li v-if="order.type_payment == 'manual'" class="flex text-sm py-1 justify-between items-center">
                                <span>No Rek Tujuan</span>
                                <span>{{ $value->payment->no_rek }}</span>
                            </li>
                            <li v-if="order.type_payment == 'manual'" class="flex text-sm py-1 justify-between items-center">
                                <span>Kode Unique</span>
                                <span>{{ $value->kode_unique }}</span>
                            </li>
                            <li v-if="order.type_payment == 'manual'" class="flex text-sm py-1 justify-between items-center">
                                <span>Total Dibayar</span>
                                <span>Rp. {{ $value->total_biaya }}</span>
                            </li>
                        @endif
                        </ul>
                        <div class="flex items-center gap-2 mt-3 justify-end">
                            @if($value->type_payment == 'gateway')
                                <a href="" class="bg-blue-600 rounded text-slate-50 block px-3 py-2 text-sm">
                                    Bayar Sekarang
                                </a>   
                                <a href="" class="px-6 py-2 bg-red-500 text-slate-50 text-sm rounded-md">
                                    Cancel
                                </a>
                            @elseif($value->type_payment == 'iorpay')
                                <a href="" class="bg-blue-600 rounded text-slate-50 block px-3 py-2 text-sm">
                                    Detail
                                </a>
                                {{-- @if($condition)
                                    
                                @endif --}}
                            @elseif($value->status_order == 'SUCCESS' && $value->status_order != 'CANCEL')
                                <a href="" class="px-6 py-2 bg-red-500 text-slate-50 text-sm rounded-md">
                                    Cancel
                                </a>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="w-full h-[400px] overflow-hidden">
            <div class="flex flex-col justify-center items-center p-6 h-full rounded-lg">
            <img src="{{ asset('assets/frontend/images/no-cart.svg') }}" class="h-[200px]" alt="Keranjang Anda Masih Kosong">
            <div class="py-3 text-center">
                <span class="font-bold text-lg">Transaksi Anda Masih Kosong</span>
            </div>
            </div>
        </div>
    @endif
</div>

{!! renderScript('script-transaksi') !!}