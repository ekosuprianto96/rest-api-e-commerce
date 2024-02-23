@php
  $user = Auth::user()->toko;
@endphp
<div class="min-h-full overflow-hidden">
    <div class="flex items-center p-3 lg:mb-3 overflow-hidden border-b-2">
      <div class="flex items-center justify-start gap-3 min-w-full overflow-hidden">
        <div v-if="user.account.image" style="background-image: url({{ $user->image ?? config('app.logo') }})" class="border bg-cover bg-center min-w-[40px] min-h-[40px] rounded-full"></div>
        <a href="" class="overflow-hidden w-[50%]">
          <h4 class="font-bold text-[0.8em] truncate">{{ $user->nama_toko }}</h4>
          <span class="block text-[0.6em]">
            {{ $user->username }}
          </span>
          <!-- <span class="block text-[0.6em] text-green-500">
            <i class="ri-verified-badge-fill text-blue-600"></i> Akun Terverifikasi
          </span> -->
        </a>
      </div>
    </div>
    <div v-if="user.detail_toko.status_toko == 'APPROVED'" class="w-full py-2 lg:block hidden">
      <a href="{{ route('user.dashboard', getUserName()) }}" class="px-6 py-2 text-sm text-center bg-blue-600 rounded-md w-full block text-slate-50">Upload Produk</a>
    </div>
    
    @auth
      <ul class="h-max lg:block hidden">
        @isset(Auth::user()->toko)
            <li class="mb-2">
                <a href="{{ route('toko.dashboard') }}" class="py-2 {{ isActiveMenu('toko.dashboard') }} w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                    <i class="ri-store-2-fill me-1"></i> <span>Dasbord Toko</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('toko.daftar-order') }}" class="py-2 {{ isActiveMenu('toko.daftar-order') }} w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                  <i class="ri-shopping-bag-3-fill me-1"></i> <span>Daftar Order</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('toko.daftar-produk') }}" class="py-2 {{ isActiveMenu('toko.daftar-produk') }} w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                  <i class="ri-shopping-bag-fill me-1"></i> <span>Daftar Produk</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('toko.settings') }}" class="py-2 {{ isActiveMenu('toko.settings') }} w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                  <i class="ri-list-settings-fill me-1"></i> <span>Profile Toko</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('user.dashboard', getUserName()) }}" class="py-2 {{ isActiveMenu('user.dashboard') }} w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                  <i class="ri-user-received-fill me-1"></i> <span>Kembali</span>
                </a>
            </li>
        @endisset
      </ul>
    @endauth
</div>