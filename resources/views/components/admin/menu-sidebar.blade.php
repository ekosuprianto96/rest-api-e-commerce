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
    <x-admin.menu.sub-menu :active-class="$title == 'Pemintaan Withdraw' ? 'active' : ''" :route="route('admin.iorpay.permintaan-withdraw')" :menu-name="'Permintaan Withdraw'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Konfirmasi Produk' ? 'active' : ''" :route="route('admin.produk.index')" :menu-name="'Konfirmasi Produk'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar User' ? 'active' : ''" :route="route('admin.user.konfirmasi-user')" :menu-name="'Konfirmasi User'" :icon="'ri-user-follow-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Toko Belum Dikonfirmasi' ? 'active' : ''" :route="route('admin.toko.konfirmasi-toko')" :menu-name="'Konfirmasi Toko'" :icon="'ri-checkbox-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Produk --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Daftar Produk'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Toko' ? 'active' : ''" :route="route('admin.toko.index')" :menu-name="'Daftar Toko'" :icon="'ri-store-3-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Transaksi --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Transaksi' ? 'active' : ''" :route="route('admin.transaksi.index')" :menu-name="'Daftar Transaksi'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Order' ? 'active' : ''" :route="route('admin.order.daftar-order')" :menu-name="'Daftar Order'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Transaksi Withdraw' ? 'active' : ''" :route="route('admin.transaksi.withdraw.index')" :menu-name="'Transaksi Withdraw'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Transaksi Topup' ? 'active' : ''" :route="route('admin.transaksi.topup.index')" :menu-name="'Transaksi Topup'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Pendapatan --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Detail Pendapatan' ? 'active' : ''" :route="route('admin.pendapatan')" :menu-name="'Detail Pendapatan'" :icon="'ri-money-dollar-box-line'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Detail Saldo' ? 'active' : ''" :route="route('admin.detail-saldo')" :menu-name="'Detail Saldo'" :icon="'ri-money-dollar-box-line'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Analityc Pendapatan Toko' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Analityc Pendapatan Toko'" :icon="'ri-money-dollar-box-line'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">

    {{-- Menu Setting --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Settings Website' ? 'active' : ''" :route="route('admin.settings.index')" :menu-name="'Setting Website'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    {{-- <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Setting Payment'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Daftar Chat'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu> --}}
    <hr class="sidebar-divider">
    

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>