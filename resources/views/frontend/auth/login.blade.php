@extends('frontend.layouts.index', ['title' => 'Login Member'])

@section('content')
    <x-frontend.layouts.container>
        <div class="flex py-4 justify-center items-center min-h-[80vh]">
            <form action="{{ route('login.authenticated') }}" method="POST" class="rounded-lg shadow-lg lg:p-6 bg-white border">
                @csrf
              <div class="grid lg:grid-cols-2 grid-cols-1">
                <div class="w-full lg:flex hidden justify-center items-center">
                  <img src="{{ asset('assets/frontend/images/login.svg') }}" width="400" alt="Illustration Login">
                </div>
                <div class="border p-4 rounded-lg flex justify-center items-center flex-col">
                  <div class="p-4 text-center mb-4">
                    <p class="font-bold">Hai, Kami punya banyak promo untuk anda</p>
                  </div>
                  <div class="w-full">
                    <div class="relative overflow-hidden w-full ">
                        <label for="email" class="text-[0.7em] absolute top-2 left-4 text-blue-600">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm" 
                            placeholder="Masukkan Email Valid"
                        >
                    </div>
                    @error('email')
                        <div class="mt-2">
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        </div>
                    @enderror
                    <div class="relative overflow-hidden w-full my-3">
                        <label for="password" class="text-[0.7em] absolute top-2 left-4 text-blue-600">Password</label>
                        <input 
                            type="password" 
                            id="password"
                            name="password"
                            class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm" 
                            placeholder="Password"
                        >
                    </div>
                    @error('password')
                        <div class="mt-2">
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        </div>
                    @enderror
                    <div class="relative overflow-hidden w-full mb-3">
                      <button type="submit" class="px-6 py-3 bg-blue-600 text-slate-50 rounded-md w-full">Sign In</button>
                    </div>
                    <!-- <div class="mb-4 relative overflow-hidden w-full">
                      <button class="px-6 py-3 border text-slate-400 rounded-md w-full"><i class="ri-google-fill text-blue-600"></i> Masuk Dengan Akun Google</button>
                    </div> -->
                    <div class="text-xs text-center mb-4">
                      <a href="" class="text-blue-600 hover:text-blue-500">Lupa Password</a>
                    </div>
                    <div class="text-xs text-center">
                      <p>Belum punya akun ? <br>Registarsi <a href="" class="text-blue-600 hover:text-blue-500">Disni</a></p>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            {{-- <div v-else-if="userBanned && !notVerified" class="rounded-lg lg:p-6 bg-white">
              <div class="flex justify-center items-center flex-col p-6">
                <img class="lg:w-[280px] w-[200px] mb-4" src="@/assets/images/user-banned.svg" alt="Banned">
                <h3 class="font-bold text-[1.3em] text-center mb-4">Mohon Maaf!</h3>
                <p class="text-center">Akun Anda telah diblokir sementara karena aktivitas yang melanggar kebijakan kami.</p>
              </div>
            </div>
            <div v-else-if="!userBanned && notVerified" class="rounded-lg lg:p-6 bg-white">
              <div class="flex justify-center items-center flex-col p-6">
                <img class="lg:w-[280px] w-[200px] mb-4" src="@/assets/images/not-verified.svg" alt="Not Verified">
                <h3 class="font-bold lg:text-[1.3em] text-[1.1em] mb-4 text-center">Mohon Maaf!, Email Belum Dikonfirmasi</h3>
                <p class="text-center lg:text-md text-sm">Akun Anda sudah hampir siap digunakan! Silakan periksa email Anda dan klik tautan konfirmasi untuk menyelesaikan proses pendaftaran.</p>
                <div class="mt-6">
                  <button v-if="!is_loading" @click="resendingUlangEmail" class="px-6 py-3 bg-green-500 text-slate-50 rounded-md text-sm w-full">Kirim Ulang Email</button>
                  <button v-else type="button" :disabled="is_loading" @click="resendingUlangEmail" class="px-3 text-center py-2 bg-green-300 rounded-md text-sm text-slate-50 w-full">
                    <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#ffffff"/>
                      <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"/>
                    </svg>
                    Loading ...
                  </button>
                </div>
              </div>
            </div> --}}
          </div>
    </x-frontend.layouts.container>
@endsection