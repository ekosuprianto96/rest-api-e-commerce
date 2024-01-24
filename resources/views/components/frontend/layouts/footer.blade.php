<footer class="min-w-screen h-max bg-blue-950 py-8">
    <x-frontend.layouts.container>
      <div class="grid lg:grid-cols-4 grid-cols-1 gap-3">
        <div class="lg:p-4">
          <div class="flex items-center mb-3">
            <img src="{{ config('app.logo') }}" class="w-[40px] me-2 lg:w-[55px]">
            <h4 class="text-slate-50 font-bold text-[1.5em]">{{ config('app.name') }}</h4>
          </div>
          <p class="text-sm text-slate-50 mb-3">{{ getSettings('tagline') }}</p>
        </div>
        @foreach(\App\Models\KategoriArtikel::with(['artikel'])->get()->take(3) as $key => $value)
            <div class="lg:p-4">
                <div class="mb-4">
                    <h4 class="text-slate-50 font-bold text-[1.3em]">{{ $value->nama }}</h4>
                </div>
                <ul>
                    @foreach($value->artikel as $key => $artikel)
                        <li>
                            <a href="" class="text-slate-50 text-sm py-2 block">{{ $artikel->display_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        <!-- <div class="lg:p-4">
          <div class="mb-4">
            <h4 class="text-slate-50 font-bold text-[1.3em]">Informasi</h4>
          </div>
          <ul>
            <li><router-link to="/" class="text-slate-50 text-sm py-2 block">Terms & Conditions</router-link></li>
            <li><router-link to="/" class="text-slate-50 text-sm py-2 block">Sejarah Lingga Store</router-link></li>
            <li><router-link to="/" class="text-slate-50 text-sm py-2 block">Transaksi</router-link></li>
            <li><router-link to="/" class="text-slate-50 text-sm py-2 block">Pengembalian Dana</router-link></li>
            <li><router-link to="/" class="text-slate-50 text-sm py-2 block">Produk Yang Dilarang</router-link></li>
          </ul>
        </div>
        <div class="lg:p-4">
          <div class="mb-4">
            <h4 class="text-slate-50 font-bold text-[1.3em]">Ikuti Kami</h4>
          </div>
          <ul class="flex items-center gap-3 mb-4">
            <li>
              <router-link to="/" class="text-slate-50 text-lg py-2 block">
                <i class="ri-facebook-fill"></i>
              </router-link>
            </li>
            <li>
              <router-link to="/" class="text-slate-50 text-lg py-2 block">
                <i class="ri-instagram-fill"></i>
              </router-link>
            </li>
            <li>
              <router-link to="/" class="text-slate-50 text-lg py-2 block">
                <i class="ri-youtube-fill"></i>
              </router-link>
            </li>
          </ul>
          <div class="">
            <p class="text-slate-50 mb-3">Langganan Newsletter Kami</p>
            <div class="w-full relative">
              <input type="email" placeholder="Email" class="w-full bg-slate-50 rounded-md h-[35px] focus:outline-none px-3">
              <button class="px-6 py-2 bg-blue-600 text-sm mt-3 text-slate-50 rounded-md shadow-lg">Langganan</button>
            </div>
          </div>
        </div> -->
      </div>
      <div class="flex flex-wrap items-end lg:gap-0 gap-3 py-8">
        <div class="lg:w-1/2 w-full lg:order-1 order-2 flex items-end min-h-full">
          <p class="text-slate-50 text-sm">&copy;Copy Right <a href="{{ route('home') }}" class="text-blue-600">{{ config('app.name') }}</a> {{ date('Y') }}</p>
        </div>
        <div class="lg:w-1/2 w-full lg:text-end lg:order-2 order-1">
          <p class="mb-3 text-sm text-slate-50">Metode Pembayaran</p>
          <div class="flex items-center gap-3 justify-end">
            <!-- <img src="https://iorsel.com/assets/img/payment/visa.svg" alt="Payment Visa" width="50">
            <img src="https://iorsel.com/assets/img/payment/mastercard.svg" alt="Payment Master Card" width="50">
            <img src="https://iorsel.com/assets/img/payment/maestro.svg" alt="Payment Maestro" width="50"> -->
            <img src="data:image/png;base64,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" alt="Payment Maestro" width="50">
            <img src="https://statik.tempo.co/data/2020/01/21/id_908083/908083_720.jpg" alt="Payment Maestro" width="50">
            <img src="https://cdn.antaranews.com/cache/1200x800/2017/12/logo_BNI.jpg" alt="Payment Maestro" width="50">
            <img src="https://30848798-2.b-cdn.net/wp-content/uploads/2020/07/Gambar-3-Cara-top-up-Shopeepay-di-Indomaret.jpg" alt="Payment Maestro" width="50">
            <img src="https://alfamart.co.id/frontend/ico/richlink.jpg" alt="Payment Maestro" width="50">
            <img src="https://asset.kompas.com/crops/VvrdofkcM7qE_w4vbJy-msi5fzE=/0x0:780x520/780x390/data/photo/2020/11/05/5fa4006d6dbde.jpg" alt="Payment Maestro" width="50">
          </div>
        </div>
      </div>
    </x-frontend.layouts.container>
</footer>