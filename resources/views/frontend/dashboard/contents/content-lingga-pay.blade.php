@php
    $user = Auth::user();
@endphp
<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>

    <div class="rounded-lg shadow-lg mt-2 bg-white border px-3 py-4 overflow-hidden">
        <div class="rounded-lg lg:p-4 overflow-hidden">
          <div class="flex justify-between items-center mb-3">
            <span>Wallet Kamu</span>
            <div class="flex justify-start items-center">
                @if($user->image)
                    <div style="background-image: url({{ $user->image }})" class="border bg-cover bg-center w-[40px] h-[40px] rounded-full"></div>
                @else
                    <div style="background-image: url({{ config('app.logo') }})" style="filter: contrast(0.3);" class="border bg-cover bg-center w-[40px] h-[40px] rounded-full"></div>
                @endif
            </div>
          </div>
          <div class="flex justify-between items-center mb-3">
            <span class="font-semibold text-[1.1em] block">
              <span class="block font-bold text-sm">Total Saldo</span>
              <span class="block text-[1.3em]">Rp. {{ number_format($user->iorPay->saldo, 0, 0, '.') }}</span>
            </span>
            <button class="lg:px-6 px-2 py-2 text-sm flex justify-end items-center text-blue-500 rounded-md">
              <i class="ri-add-circle-fill me-2"></i> Top Up
            </button>
          </div>
          <div class="flex justify-center items-center gap-2">
            <button class="lg:px-6 px-2 hover:bg-slate-300 hover:text-slate-50 w-1/2 py-2 text-sm text-green-500 border-green-500 border rounded-md">
              <i class="ri-refresh-line"></i> Refresh
            </button>
            <button class="lg:px-6 px-2 w-1/2 py-2 text-sm bg-green-500 text-slate-50 rounded-md">
                <i class="ri-bank-card-fill"></i> Withdraw
            </button>
          </div>
        </div>
      </div>
      <div class="rounded-lg mt-4 bg-white">
        <div class="rounded-lg p-4">
          <span class="font-bold block py-2 border-b-2">History Transaksi</span>
        </div>
        @if(@count($user->iorPay->trx) > 0)
            <ul class="overflow-y-auto mt-4 max-h-[600px]">
                @foreach(App\Models\TrxIorpay::where('kode_pay', $user->iorPay->kode_pay)->latest()->get() as $key => $value)
                    <li class="p-4 border mb-3 rounded-lg list-transaksi">
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-sm flex items-center">{{ $value->keterangan }}
                                    @if($value->status_trx == '0')
                                        <span class="bg-red-500 ms-2 text-[0.8em] px-3 rounded-lg text-slate-50">Belum Dikonfirmasi</span>
                                    @elseif($value->status_trx == 'SUCCESS')
                                        <span class="bg-green-500 ms-2 text-[0.8em] px-3 rounded-lg text-slate-50">Success</span>
                                    @elseif($value->status_trx == 'PENDING')
                                        <span class="bg-yellow-500 ms-2 text-[0.8em] px-3 rounded-lg text-slate-50">Pending</span>
                                    @elseif($value->status_trx == 'CANCEL')
                                        <span class="bg-red-500 ms-2 text-[0.8em] px-3 rounded-lg text-slate-50">Cancel</span>
                                    @endif
                                </span>
                                <span class="text-xs text-slate-400 block mt-2">{{ $value->created_at->format('Y-m-d') }}</span>
                                <div class="flex flex-col">
                                    @if($value->type_pay == 'DEBIT')
                                        <span class="text-green-500">Rp. +{{ number_format($value->total_fixed, 0, 0, '.') }}</span>
                                    @else
                                        <span v-else class="text-red-500">Rp. -{{ number_format($value->total_fixed, 0, 0, '.') }}</span>
                                    @endif
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
                                    <span>{{ $value->no_trx }}</span>
                                </li>
                                <li class="flex text-sm py-1 justify-between items-center">
                                    <span>Tanggal Transaksi</span>
                                    <span>{{ $value->created_at->format('Y-m-d') }}</span>
                                </li>
                                @if($value->jenis_pembayaran == 'linggaPay')
                                    <li class="flex text-sm py-1 justify-between items-center">
                                        <span>Total Transaksi</span>
                                        <span>Rp. {{ number_format($value->total_fixed, 0, 0, '.') }}</span>
                                    </li>
                                @endif
                                @if($value->type_payment == 'manual')
                                    <li class="flex text-sm py-1 justify-between items-center">
                                        <span>Bank Tujuan</span>
                                        <span>{{ $value->payment->payment_name }}</span>
                                    </li>
                                    <li class="flex text-sm py-1 justify-between items-center">
                                        <span>No Rek Tujuan</span>
                                        <span>{{ $value->payment->no_rek }}</span>
                                    </li>
                                    <li class="flex text-sm py-1 justify-between items-center">
                                        <span>Kode Unique</span>
                                        <span>{{ $value->kode_unique }}</span>
                                    </li>
                                    <li class="flex text-sm py-1 justify-between items-center">
                                        <span>Total Dibayar</span>
                                        <span>Rp. {{ $value->total_trx }}</span>
                                    </li>
                                    <li class="flex text-sm py-1 justify-between items-center">
                                        <span>Total Dibayar</span>
                                        <span>Rp. {{ $value->keterangan }}</span>
                                    </li>
                                @endif
                            </ul>
                            <div class="flex items-center gap-2 mt-3 justify-end">
                                @if($value->type_payment == 'gateway')
                                    <a href="" class="bg-blue-600 rounded text-slate-50 block px-3 py-2 text-sm">
                                        Bayar Sekarang
                                    </a>   
                                @elseif($value->type_payment == 'iorpay')
                                    <a href="" class="bg-blue-600 rounded text-slate-50 block px-3 py-2 text-sm">
                                        Detail
                                    </a>
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
</div>

{!! renderScript('script-transaksi') !!}

