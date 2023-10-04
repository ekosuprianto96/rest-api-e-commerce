<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    {{-- Start Logo --}}
    <x-admin.logo-sidebar class="logo-sidebar" :image="null" :alt="'Logo Kasirku'" :logo-text="'IORSEL'"></x-admin.logo-sidebar>
    {{-- End Logo --}}

    <hr class="sidebar-divider my-0">
    
    {{-- Menu Dashboard --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Dashboard' ? 'active' : ''" :route="route('admin.dashboard')" :menu-name="'Dashboard'" :icon="'fas fa-fw fa-tachometer-alt'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Payment --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi Pembayaran' ? 'active' : ''" :route="route('admin.payment.index')" :menu-name="'Konfirmasi Pembayaran'" :icon="'ri-list-check-3'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi Topup' ? 'active' : ''" :route="route('admin.iorpay.permintaan-topup')" :menu-name="'Konfrimasi Topup'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Produk --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi Produk' ? 'active' : ''" :route="route('admin.produk.index')" :menu-name="'Konfirmasi Produk'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Daftar Produk'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu User Member --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi User' ? 'active' : ''" :route="route('admin.user.index')" :menu-name="'Daftar User'" :icon="'ri-user-3-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar User' ? 'active' : ''" :route="route('admin.user.konfirmasi-user')" :menu-name="'Konfirmasi User'" :icon="'ri-user-follow-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Seller --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi Produk' ? 'active' : ''" :route="route('admin.toko.index')" :menu-name="'Daftar Toko'" :icon="'ri-store-3-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.toko.konfirmasi-toko')" :menu-name="'Konfirmasi Toko'" :icon="'ri-checkbox-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Transaksi --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi Produk' ? 'active' : ''" :route="route('admin.produk.index')" :menu-name="'Daftar Transaksi'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Detail Transaksi'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Setting --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi Produk' ? 'active' : ''" :route="route('admin.produk.index')" :menu-name="'Setting Website'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Setting Payment'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Daftar Chat'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">
    

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>