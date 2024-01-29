<style>
    .show-menu {
        /* display: block; */
        top: 0;
        transition: 0.3s;
        scroll-behavior: smooth;
    }
    .hide-menu {
     /* display: none;    */
     top: 100%;
     transition: 0.3s;
    }
    .scroll-custom::-webkit-scrollbar {
        background-color: rgba(189, 189, 189, 0);
        width: 2px;
    }

</style>
<header class="bg-gradient-to-br max-w-screen shadow-lg from-blue-600 sticky top-0 z-[50] to-blue-900 py-2 lg:py-3">
    <div class="px-2 w-full lg:px-28">
        <div class="lg:grid hidden grid-cols-2 mb-3">
            <div class="w-full">
                <ul class="flex justify-start items-center">
                    <li><a href="" class="pr-3 text-sm border-r-2 text-slate-50"><i class="ri-customer-service-fill"></i> Kontak Kami</a></li>
                    <li><a href="" class="px-3 text-sm text-slate-50"><i class="ri-chat-smile-2-fill"></i> Chat</a></li>
                </ul>
            </div>
            <div class="">
                <ul class="flex justify-end items-center">
                    @auth
                        <li v-if="user.account.auth"><a href="{{ route('logout') }}" class="px-3 text-sm border-r-2 text-slate-50"><i class="ri-user-fill"></i> Logout</a></li>
                    @endauth
                    @guest
                        <li v-if="!user.account.auth"><a href="" class="px-3 text-sm text-slate-50"><i class="ri-user-fill"></i> Register</a></li>
                    @endguest
                    @auth
                        <li v-else><a href="{{ route('user.dashboard', getUserName()) }}" class="px-3 text-sm border-l-2 text-slate-50"><i class="ri-dashboard-2-fill"></i> Dashboard</a></li>                        
                    @endauth
                </ul>
            </div>
        </div>
        <div class="lg:grid hidden grid-cols-5 gap-4 relative">
            <div class="col-span-1 flex items-center">
                <img src="{{ config('app.logo') }}" class="w-[50px]" alt="Logo">
                <h1 class="font-bold text-slate-50 ms-3 text-lg">
                    <a href="{{ route('home') }}">
                    {{ config('app.name') }}
                    </a>
                </h1>
            </div>
            <div class="col-span-3">
                <div class="rounded-lg bg-slate-50 py-2 w-full overflow-hidden flex items-center h-[40px]">
                    <div class="w-[25%] px-2 h-full flex items-center justify-center border-r-2">
                    <span data-target="dropDownKategori" id="button-drop" class="text-blue-700 whitespace-nowrap block text-sm hover:bg-blue-300 p-1 hover:cursor-pointer h-max rounded-lg">
                        Semua Kategori
                        <i data-target="dropDownKategori" class="ri-arrow-down-s-line ms-2"></i>
                    </span>
                    </div>
                    <div class="w-[80%] relative flex items-center">
                    <input v-model="keyword" type="text" class="px-3 bg-slate-50 focus:outline-none w-full py-3 h-full text-[0.9em]" placeholder="Cari barang disini...">
                    <button type="button" class="absolute right-2 p-2 rounded-lg h-[30px] flex justify-center items-center z-50 text-slate-50 shadow-lg bg-gradient-to-tr from-blue-500 to-blue-600">
                        <i class="ri-search-line"></i>
                    </button>
                    </div>
                </div>
            </div>
      
            <div class="col-span-1 flex items-center">
                <div class="{{ Auth::check() ? 'grid grid-cols-3 w-full gap-2' : 'flex justify-between items-center w-full gap-2' }}">
                    <a href="{{ Auth::check() ? '' : route('login') }}" class="bg-white w-max px-2 py-1 rounded-md relative">
                        <i class="ri-shopping-cart-fill text-blue-500"></i>
                        <span id="countCartDesktop" class="w-[20px] h-[20px] text-[0.6em] text-slate-50 bg-red-500 rounded-full flex justify-center items-center absolute -top-2 -right-2">{{ $totalCart ?? 0 }}</span>
                    </a>
                    <a href="{{ Auth::check() ? '' : route('login') }}" class="bg-white px-2 w-max py-1 rounded-md">
                        <i class="ri-heart-fill text-blue-500"></i>
                    </a>
                    @guest
                        <a href="{{ route('login') }}" v-if="!user.account.auth" class="border text-sm text-blue-500 bg-slate-50 px-8 py-1 rounded-full">
                            Login
                        </a>
                    @endguest
                    @auth
                        <a href="{{ route('home') }}" class="bg-white w-max px-2 py-1 rounded-md relative">
                            <i class="ri-store-3-fill text-blue-500"></i>
                        </a>   
                    @endauth
                </div>
            </div>
        </div>
        <div id="dropDownKategori" style="display: none" class="border transition-all absolute top-[85%] p-4 left-[25%] overflow-hidden shadow-lg w-[200px] rounded-lg bg-slate-50 z-[9999999999999999999999999999]">
            <ul class="text-sm">
                @foreach(\App\Models\Kategori::all() as $key => $value)
                <li class="py-1"><a href="" class="hover:text-blue-600 block">{{ $value->nama_kategori }}</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Navbar Mobile -->
        <div class="flex w-full items-center py-2 lg:hidden">
            <div class="w-[75%] relative flex items-center">
                <input v-model="keyword" type="text" class="w-full h-[40px] bg-slate-50 rounded-lg px-3 focus:outline-0 focus:border-2 focus:border-slate-200" placeholder="Cari produk disini...">
                <button type="button" class="absolute right-2 p-2 rounded-lg h-[30px] flex justify-center items-center z-50 text-slate-50 shadow-lg bg-blue-500">
                    <i class="ri-search-line"></i>
                </button>
            </div>
            <div class="w-[25%] pl-2 flex justify-between items-center text-center">
                @guest
                    <a href="" class="flex text-slate-100 items-center">
                        <i class="ri-login-box-fill text-slate-100 text-[2em]"></i> Masuk
                    </a>
                @endguest
                @auth
                    <a href="" class="bg-white w-max px-2 py-1 rounded-md relative">
                        <i class="ri-shopping-cart-fill text-blue-500"></i>
                        <span id="countCartMobile" class="w-[20px] h-[20px] text-[0.6em] text-slate-50 bg-red-500 rounded-full flex justify-center items-center absolute -top-2 -right-2">{{ $totalCart ?? 0 }}</span>
                    </a>
                    <a href="" class="bg-white w-max px-2 py-1 rounded-md relative">
                        <i class="ri-chat-smile-2-fill text-blue-500"></i>
                        <span class="w-[20px] h-[20px] text-[0.6em] text-slate-50 bg-red-500 rounded-full flex justify-center items-center absolute -top-2 -right-2">{{ $totalChat ?? 0 }}</span>
                    </a>                    
                @endauth
            </div>
        </div>
    </div>
    <div id="menuMobile" class="fixed lg:hidden hide-menu bottom-[9%] top-0 left-0 right-0 bg-black bg-opacity-20 px-3 py-3 z-50">
        <div class="relative w-full h-full">
            <div class="absolute transition-all min-w-full top-0 bottom-0 min-h-full rounded-lg shadow-lg overflow-y-auto scroll-custom">
                <div id="wrapperMenu" class="w-full overflow-y-auto scroll-custom h-full transition-all rounded-lg p-4 absolute bg-white z-40">
                    <div class="col-span-4 py-4 h-max">
                        <span class="font-bold text-[1.3em]">Menu</span>
                    </div>
                    @auth
                        <div v-if="user.detail_toko.status_toko == 'APPROVED'" class="w-full py-2 lg:block">
                            <button class="px-6 py-2 text-sm text-center bg-blue-600 rounded-md w-full block text-slate-50">Upload Produk</button>
                        </div>
                        <ul class="h-max pb-3 border-b-2 overflow-y-auto">
                            <li class="mb-2"><button type="button" :class="$route.name == 'iorpay' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 hover:bg-blue-500 w-full flex items-center relative hover:text-slate-50 rounded-lg px-2 text-sm">
                            <i class="ri-wallet-3-fill me-1"></i> LinggaPay <button type="button" class="absolute right-2 text-xs">Aktifkan</button></button></li>
                            <li class="mb-2"><button type="button" :class="$route.name == 'keranjang' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-shopping-cart-2-fill"></i> Keranjang</button>
                            </li>
                            <li class="mb-2"><button type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-percent-fill"></i> Komisi Referal</button></li>
                            <li class="mb-2"><button type="button" :class="$route.name == 'daftar-pesan' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 relative hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-chat-4-fill"></i> Pesan <span v-if="notification.pesan > 0" class="absolute right-2 rounded-full w-[20px] h-[20px] flex justify-center items-center text-[0.6em] top-[25%] bg-red-500 text-slate-50">1</span></button></li>
                            <!-- <li class="mb-2"><button type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start" @click="getRoute('dashboard')">
                            <i class="ri-edit-fill"></i> Post Permintaan</button></li> -->
                            <li class="mb-2"><button type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-survey-fill"></i> Daftar Transaksi</button></li>
                            <li class="mb-2"><button type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-survey-fill"></i> Daftar Pesanan</button></li>
                            <li class="mb-2"><button type="button" :class="$route.name == 'detail-akun' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-user-settings-fill"></i> Pengaturan Akun</button></li>
                            <li class="mb-2"><button type="button" :class="$route.name == 'pemberitahuan' ? 'bg-blue-500 text-slate-50' : ''" class="py-2 relative hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-user-settings-fill"></i> Pemberitahuan <span class="absolute right-2 rounded-full w-[20px] h-[20px] flex justify-center items-center text-[0.6em] top-[25%] bg-red-500 text-slate-50">0</span></button></li>
                        </ul>
                        <ul :class="user.detail_toko.status_toko == 'APPROVED' ? '' : 'pb-3 mt-3'" class="h-max">
                            <li v-if="user.detail_toko.status_toko == 'PENDING' || user.detail_toko.status_toko == ''"><button :disabled="user.detail_toko.status_toko == 'PENDING' || user.detail_toko.status_toko == 'REJECT'" type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 w-full flex items-center relative rounded-lg px-2 text-sm text-start">
                            <i class="ri-store-2-fill me-1"></i> Buka Toko <button v-if="user.detail_toko.status_toko == 'PENDING'" type="button" class="absolute right-2 text-xs">PENDING</button></button></li>
                            <!-- <li class="mb-2"><button type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start" @click="getRoute('dashboard')">
                            <i class="ri-bank-card-fill"></i> Pembayaran</button></li> -->
                        </ul>
                        <ul v-if="user.detail_toko.status_toko == 'APPROVED'" class="h-max mt-3">
                            <li class="mb-2"><button type="button" class="py-2 relative hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-user-settings-fill"></i> Daftar Order <span v-if="notification.order_toko > 0" class="absolute right-2 rounded-full w-[20px] h-[20px] flex justify-center items-center text-[0.6em] top-[25%] bg-red-500 text-slate-50">0</span></button></li>
                            <li class="mb-2"><button class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 w-full text-start block text-sm">
                            <i class="ri-user-settings-fill"></i> Daftar Produk</button></li>
                            <li class="mb-2"><button type="button" class="py-2 hover:bg-blue-500 hover:text-slate-50 rounded-lg px-2 block text-sm w-full text-start">
                            <i class="ri-store-2-fill"></i> Setting Toko</button></li>
                        </ul>
                        <div v-if="user.account.auth" class="w-full py-2">
                            <button class="px-6 py-2 text-sm text-center bg-blue-600 rounded-md w-full block text-slate-50">Logout</button>
                        </div>   
                    @endauth
                    @guest
                        <div class="w-full h-[400px] flex flex-col justify-center items-center">
                            <div>
                                <img src="{{ asset('assets/frontend/images/login-menu.svg') }}" width="180" alt="">
                            </div>
                            <ul class="flex flex-col gap-3 py-4 justify-center items-center w-full">
                                <li class="w-full h-max">
                                <button class="px-6 py-2 bg-blue-500 w-full block text-center text-slate-50 rounded-md shadow-md">Login</button>
                                </li>
                                <li class="w-full h-max">
                                <button class="px-6 py-2 bg-blue-500 w-full block text-center text-slate-50 rounded-md shadow-md">Register</button>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
    
    <nav class="fixed block lg:hidden bottom-0 overflow-visible py-1 left-0 right-0 bg-slate-100 z-[90]" style="box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.308);">
        <ul class="grid grid-cols-5 gap-3">
            <li>
                <a href="" class="h-full flex justify-center w-full items-center flex-col">
                    <i class="ri-home-fill text-[1.5em] text-blue-600"></i>
                    <span class="text-[0.6em]">Home</span>
                </a>
            </li>
            <li>
            <button type="button" class="h-full flex justify-center w-full items-center flex-col">
                <i class="ri-heart-fill text-[1.5em] text-blue-600"></i>
                <span class="text-[0.6em]">Whislist</span>
            </button>
            </li>
            <li class="relative flex justify-center z-50">
            <button data-target="menuMobile" id="mainMenu" type="button" class="border-4 hover:-top-5 transition-all hover:bg-blue-400 border-blue-300 bg-blue-600 rounded-full -top-3 absolute w-[60px] h-[60px] flex justify-center  items-center flex-col">
                <i class="ri-apps-fill text-slate-50 text-[1.8em]" data-target="menuMobile"></i>
            </button>
            </li>
            <li>
            <button type="button" class="h-full flex justify-center w-full items-center flex-col">
                <i class="ri-box-1-fill text-[1.5em] text-blue-600"></i>
                <span class="text-[0.6em]">Pesanan</span>
            </button>
            </li>
            <li>
            <button type="button" class="h-full flex justify-center w-full items-center flex-col">
                <i class="ri-user-fill text-[1.5em] text-blue-600"></i>
                <span class="text-[0.6em]">Akun</span>
            </button>
            </li>
        </ul>
    </nav>
</header>

@include('frontend.layouts.script-menu')