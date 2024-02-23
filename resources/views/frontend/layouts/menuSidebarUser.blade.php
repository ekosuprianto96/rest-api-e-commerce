@php
  $user = Auth::user();
@endphp
<div>
    <div class="flex items-center p-3 lg:mb-3 overflow-hidden border-b-2">
      <div class="flex items-center justify-start gap-3 min-w-full overflow-hidden">
        <div style="background-image: url({{ $user->image ?? config('app.logo') }})" class="border bg-cover bg-center min-w-[40px] min-h-[40px] rounded-full"></div>
        <a href="" class="overflow-hidden w-[50%]">
          <h4 class="font-bold text-[0.8em] truncate">{{ $user->full_name }}</h4>
          <span class="block text-[0.6em]">
            {{ $user->username }}
          </span>
          <!-- <span class="block text-[0.6em] text-green-500">
            <i class="ri-verified-badge-fill text-blue-600"></i> Akun Terverifikasi
          </span> -->
        </a>
      </div>
    </div>
    <div class="w-full py-2 lg:block hidden">
      <a href="{{ route('user.dashboard', $user->username) }}" class="px-6 py-2 text-sm text-center bg-blue-600 rounded-md w-full block text-slate-50">Dasboard</a>
    </div>
    <ul class="h-max lg:block hidden">
      <li class="mb-2">
        <a href="{{ route('user.linggaPay', getUserName()) }}" class="py-2 flex items-center relative {{ isActiveMenu('user.linggaPay') }} rounded-lg px-2 text-sm">
          <i class="ri-wallet-3-fill me-1"></i> LinggaPay 
          <span class="absolute bg-green-400 text-slate-50 rounded-lg px-2 py-1 right-2 text-xs">Aktif</span>
        </a>
      </li>
      <li class="mb-2">
        <a href="{{ route('user.keranjang', getUserName()) }}" class="py-2 {{ isActiveMenu('user.keranjang') }} rounded-lg px-2 block text-sm">
          <i class="ri-shopping-cart-2-fill"></i> Keranjang
        </a>
      </li>
      <li class="mb-2">
        <a href="{{ route('user.wishlist', $user->username) }}" class="py-2 {{ isActiveMenu('user.wishlist') }} rounded-lg px-2 block text-sm">
          <i class="ri-heart-fill"></i> WishList
        </a>
      </li>
      <li class="mb-2">
        <a href="{{ route('user.affiliasi', getUserName()) }}" class="py-2 relative {{ isActiveMenu('user.affiliasi') }} rounded-lg px-2 block text-sm">
          <i class="ri-percent-fill"></i> Komisi Referal
        </a>
      </li>
      <li class="mb-2">
        <a href="" class="py-2 relative {{ isActiveMenu('') }} rounded-lg px-2 block text-sm">
          <i class="ri-chat-4-fill"></i> Pesan <span class="absolute right-2 rounded-full w-[20px] h-[20px] flex justify-center items-center text-[0.6em] top-[25%] bg-red-500 text-slate-50">{{ 0 }}</span>
        </a>
      </li>
      <li class="mb-2">
        <a href="{{ route('user.transaksi', getUserName()) }}" class="py-2 {{ isActiveMenu('user.transaksi') }} rounded-lg px-2 block text-sm">
          <i class="ri-survey-fill"></i> Daftar Transaksi
        </a>
      </li>
      <li class="mb-2">
        <a href="{{ route('user.pesanan', getUserName()) }}" class="py-2 {{ isActiveMenu('user.pesanan') }} rounded-lg px-2 block text-sm relative">
          <i class="ri-survey-fill"></i> Daftar Pesanan
        </a>
      </li>
      <li class="mb-2">
        <a href="{{ route('user.profile', getUserName()) }}" class="py-2 {{ isActiveMenu('user.profile') }} rounded-lg px-2 block text-sm">
          <i class="ri-user-settings-fill"></i> Pengaturan Akun
        </a>
      </li>
      <li class="mb-2">
        <a href="" class="py-2 {{ isActiveMenu('') }} rounded-lg px-2 block text-sm">
          <i class="ri-user-settings-fill"></i> Pemberitahuan
        </a>
      </li>
    </ul>
    @auth
      <ul class="h-max border-t-2 pt-2 lg:block hidden">
        @isset(Auth::user()->toko)
          @if(Auth::user()->toko->status_toko == 'PENDING' || Auth::user()->toko->status_toko == '')  
            <li class="mb-2">
                <a href="" class="py-2 {{ isActiveMenu('') }} w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                  <i class="ri-store-2-fill me-1"></i> Buka Toko 
                  @if(Auth::user()->toko->status_toko === 'PENDING')
                    <button type="button" class="absolute right-2 text-xs">PENDING</button>
                  @endif
                </a>
            </li>
          @elseif(Auth::user()->toko->status_toko == 'APPROVED')
            <li class="mb-2">
                <a href="{{ route('toko.dashboard') }}" class="py-2 {{ isActiveMenu('toko.dashboard') }} w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                    <i class="ri-store-2-fill me-1"></i> Dasbord Toko
                </a>
            </li>
          @endif
        @endisset
      </ul>
    @endauth
</div>