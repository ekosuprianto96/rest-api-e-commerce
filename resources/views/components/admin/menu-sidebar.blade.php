<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    {{-- Start Logo --}}
    <x-admin.logo-sidebar class="logo-sidebar" :image="config('app.logo')" :alt="'Logo '.config('app.name').''" :logo-text="config('app.name')"></x-admin.logo-sidebar>
    {{-- End Logo --}}
    
    {{-- Menu Dashboard --}}
    <x-admin.menu.sub-menu :active-class="$title == 'Dashboard' ? 'active' : ''" :route="route('admin.dashboard', Auth::user()->uuid)" :menu-name="'Dashboard'" :icon="'fas fa-fw fa-tachometer-alt'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider">
    @foreach($array_menu as $key => $value)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#{{ str_replace(' ', '', $key) }}"
                aria-expanded="true" aria-controls="{{ str_replace(' ', '', $key) }}">
                {{-- <i class="fas fa-fw fa-cog"></i> --}}
                <span>{{ $key }}</span>
            </a>
            <div id="{{ str_replace(' ', '', $key) }}" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                     <h6 class="collapse-header">{{ $key }}</h6>
                    {{-- <a class="collapse-item" href="buttons.html">Buttons</a> --}}
                    @foreach($value as $key => $menu)
                        <a class="collapse-item position-relative {{ $title == $menu->nama ? 'active' : '' }}" href="{{ route(str_replace('/', '.', $menu->url)) }}">{{ $menu->nama }}</a>
                        <span id="{{ $menu->nama_alias ?? '' }}" class="text-light {{ $menu->notif > 0 ? 'd-flex' : 'd-none' }} justify-content-center align-items-center position-absolute bg-danger" style="font-size: 0.6em;right: -9px;;width:20px;height:20px;border-radius: 50%;">{{ $menu->notif > 0 ? $menu->notif : 0  }}</span>
                        @if(\Route::current()->getName() != str_replace('/', '.', $menu->url))
                            <script>
                                $(function() {
                                    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                                        cluster: "ap1",
                                    });
                            
                                    var channel = pusher.subscribe("notification");
                                    channel.bind('{{ $alias ?? '' }}', function(response) {
                                        const audio = new Audio();
                                        audio.src = '{{ asset("assets/admin/audio/notification.wav") }}';
                                        audio.play();
                            
                                        let countMessage = parseInt($('#count_message').text());
                                    
                                        countMessage += 1;
                                        $('#count_message').text(countMessage);
                                        $('#wrapper_notification').append(`
                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-primary">
                                                        <i class="fas fa-file-alt text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small text-gray-500">December 12, 2019</div>
                                                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                                </div>
                                            </a>
                                        `);
                                        $.toast({
                                            heading: 'Order Masuk',
                                            text: response,
                                            showHideTransition: 'slide',
                                            position: 'top-right',
                                            icon: 'success'
                                        });
                                        counterNotification('{{ $alias ?? '' }}');
                                        console.log('order:', response);
                                    })
                                })
                            
                                function counterNotification(target) {
                                    const elTarget = $(`#${target}`);
                                    console.log(target, elTarget);
                                    if(elTarget != undefined) {
                                        let currentCount = parseInt($(`#${target}`).text());
                                        currentCount += 1;
                                        $(`#${target}`).text(currentCount);
                            
                                        if($(`#${target}`).hasClass('d-none')) {
                                            $(`#${target}`).removeClass('d-none');
                                            $(`#${target}`).addClass('d-flex');
                                        }
                                    }
                                }
                            </script>
                        @endif
                        {{-- <x-admin.menu.sub-menu :alias="$menu->nama_alias" :count-notif="$menu->notif" :notif="$menu->count_notif" :url="$menu->url" :active-class="$title == $menu->nama ? 'active' : ''" :route="route(str_replace('/', '.', $menu->url))" :menu-name="$menu->nama" :icon="$menu->icon"></x-admin.menu.sub-menu> --}}
                    @endforeach
                </div>
            </div>
        </li>
        {{-- <div class="px-3">
            <span class="text-light d-block pb-1" style="border-bottom: 1px solid white;"></span>
        </div> --}}
    @endforeach

    {{-- Menu Produk --}}
    {{-- <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Daftar Produk'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Toko' ? 'active' : ''" :route="route('admin.toko.index')" :menu-name="'Daftar Toko'" :icon="'ri-store-3-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider"> --}}

    {{-- Menu Transaksi --}}
    {{-- <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Transaksi' ? 'active' : ''" :route="route('admin.transaksi.index')" :menu-name="'Daftar Transaksi'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Order' ? 'active' : ''" :route="route('admin.order.daftar-order')" :menu-name="'Daftar Order'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Transaksi Withdraw' ? 'active' : ''" :route="route('admin.transaksi.withdraw.index')" :menu-name="'Transaksi Withdraw'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Transaksi Topup' ? 'active' : ''" :route="route('admin.transaksi.topup.index')" :menu-name="'Transaksi Topup'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider"> --}}

    {{-- Menu Pendapatan --}}
    {{-- <x-admin.menu.sub-menu :active-class="$title == 'Detail Pendapatan' ? 'active' : ''" :route="route('admin.pendapatan')" :menu-name="'Detail Pendapatan'" :icon="'ri-money-dollar-box-line'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Detail Saldo' ? 'active' : ''" :route="route('admin.detail-saldo')" :menu-name="'Detail Saldo'" :icon="'ri-money-dollar-box-line'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Analityc Pendapatan Toko' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Analityc Pendapatan Toko'" :icon="'ri-money-dollar-box-line'"></x-admin.menu.sub-menu>
    <hr class="sidebar-divider"> --}}

    {{-- Menu Setting --}}
    {{-- <x-admin.menu.sub-menu :active-class="$title == 'Daftar Role' ? 'active' : ''" :route="route('admin.role.index')" :menu-name="'Management Role'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Permission' ? 'active' : ''" :route="route('admin.permission.index')" :menu-name="'Daftar Permission'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Payment' ? 'active' : ''" :route="route('admin.payment.daftar-payment')" :menu-name="'Daftar Payment'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Master Menu' ? 'active' : ''" :route="route('admin.ms-menu.index')" :menu-name="'Master Menu'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Settings Website' ? 'active' : ''" :route="route('admin.settings.index')" :menu-name="'Setting Website'" :icon="'ri-checkbox-circle-fill'"></x-admin.menu.sub-menu> --}}
    {{-- <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Setting Payment'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu>
    <x-admin.menu.sub-menu :active-class="$title == 'Daftar Semua Produk' ? 'active' : ''" :route="route('admin.produk.all-produk')" :menu-name="'Daftar Chat'" :icon="'ri-shopping-cart-fill'"></x-admin.menu.sub-menu> --}}
    {{-- <hr class="sidebar-divider"> --}}
    

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>