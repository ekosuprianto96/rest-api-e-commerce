@php
  $user = Auth::user();
@endphp
<div>
    <div class="flex items-center p-3 lg:mb-3 overflow-hidden border-b-2">
      <div class="flex items-center justify-start gap-3 min-w-full overflow-hidden">
        <div v-if="user.account.image" style="background-image: url({{ $user->image ?? config('app.logo') }})" class="border bg-cover bg-center min-w-[40px] min-h-[40px] rounded-full"></div>
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
    <div v-if="user.detail_toko.status_toko == 'APPROVED'" class="w-full py-2 lg:block hidden">
      <a href="" class="px-6 py-2 text-sm text-center bg-blue-600 rounded-md w-full block text-slate-50">Upload Produk</a>
    </div>
    <ul class="h-max pb-3 border-b-2 lg:block hidden">
      <li class="mb-2">
        <a href="" class="py-2 hover:bg-blue-500 flex items-center relative hover:text-slate-50 rounded-lg px-2 text-sm">
          <i class="ri-wallet-3-fill me-1"></i> LinggaPay 
          <span class="absolute bg-green-400 text-slate-50 rounded-lg px-2 py-1 right-2 text-xs">Aktif</span>
        </a>
      </li>
      <li class="mb-2"><a href="{{ route('user.keranjang', getUserName()) }}" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm">
        <i class="ri-shopping-cart-2-fill"></i> Keranjang</a>
      </li>
      <li class="mb-2"><a href="" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm">
        <i class="ri-heart-fill"></i> WishList</a></li>
      <li class="mb-2"><a href="" class="py-2 relative hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm">
        <i class="ri-percent-fill"></i> Komisi Referal</a></li>
      <li class="mb-2"><a href="" class="py-2 relative hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm">
        <i class="ri-chat-4-fill"></i> Pesan <span class="absolute right-2 rounded-full w-[20px] h-[20px] flex justify-center items-center text-[0.6em] top-[25%] bg-red-500 text-slate-50">{{ 0 }}</span></a></li>
      <li class="mb-2"><a href="" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm">
        <i class="ri-survey-fill"></i> Daftar Transaksi</a></li>
      <li class="mb-2"><a href="" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm relative">
        <i class="ri-survey-fill"></i> Daftar Pesanan</a></li>
      <li class="mb-2"><a href="" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm">
        <i class="ri-user-settings-fill"></i> Pengaturan Akun</a></li>
      <li class="mb-2"><a href="" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm">
        <i class="ri-user-settings-fill"></i> Pemberitahuan</a></li>
    </ul>
    {{-- <ul :class="user.detail_toko.status_toko == 'APPROVED' ? '' : 'pb-3 mt-3'" class="h-max lg:block hidden">
      <li v-if="user.detail_toko.status_toko == 'PENDING' || user.detail_toko.status_toko == ''"><a :to="(user.detail_toko.status_toko == 'PENDING' || user.detail_toko.status_toko == 'REJECT') ? '' : getRoute('buka-toko')" :class="(user.detail_toko.status_toko == 'PENDING' || user.detail_toko.status_toko == 'REJECT') ? 'hover:cursor-not-allowed' : ''" type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 w-full flex items-center relative rounded-lg px-2 text-sm text-start">
          <i class="ri-store-2-fill me-1"></i> Buka Toko <button v-if="user.detail_toko.status_toko == 'PENDING'" type="button" class="absolute right-2 text-xs">PENDING</button></a></li>
      <!-- <li class="mb-2"><a class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm" :to="getRoute('dashboard')">
        <i class="ri-bank-card-fill"></i> Pembayaran</a></li> -->
    </ul> --}}
    {{-- <ul v-if="user.detail_toko.status_toko == 'APPROVED'" class="h-max lg:block hidden pb-3 mt-3">
      <li class="mb-2"><a :class="$route.name == 'order-toko' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 relative hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm" :to="getRoute('order-toko')">
        <i class="ri-user-settings-fill"></i> Daftar Order <span v-if="notification.order_toko > 0" class="absolute right-2 rounded-full w-[20px] h-[20px] flex justify-center items-center text-[0.6em] top-[25%] bg-red-500 text-slate-50">{{ notification.order_toko }}</span></a></li>
      <li class="mb-2"><a :class="$route.name == 'daftar-produk' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm" :to="getRoute('daftar-produk')">
        <i class="ri-user-settings-fill"></i> Daftar Produk</a></li>
      <li class="mb-2"><a :class="$route.name == 'detail-toko' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm" :to="getRoute('detail-toko')">
        <i class="ri-store-2-fill"></i> Setting Toko</a></li>
      <!-- <li class="mb-2"><a class="py-2 relative hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm" :to="getRoute('notifikasi-toko')">
        <i class="ri-notification-3-fill"></i> Pesan Toko <span v-if="notification.pesan_toko > 0" class="absolute right-2 rounded-full w-[20px] h-[20px] flex justify-center items-center text-[0.6em] top-[25%] bg-red-500 text-slate-50">{{ notification.pesan_toko }}</span></a></li> -->
      <!-- <li class="mb-2"><a class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm" :to="getRoute('dashboard')">
        <i class="ri-bank-card-fill"></i> Atur Pembayaran</a></li> -->
    </ul> --}}
</div>