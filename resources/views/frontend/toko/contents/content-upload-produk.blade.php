@php
    $user = Auth::user();
    $session = session()->get('produkTemp');
@endphp
<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>
  
    <div class="bg-green-200 mt-4 rounded-lg p-3 mb-3">
        <span class="text-sm font-bold block mb-2">Perhatian:</span>
        <p class="text-xs">Harap membaca terlebih dahulu aturan dan cara upload produk, silhakan lihat cara upload produk 
          <router-link :to="{name: 'artikel', params: {group: 6, slugartikel: 'aturan-dan-cara-upload-produk'}}" class="text-blue-500">disni</router-link></p>
      </div>
      <form action="{{ route('toko.produk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="flex justify-between gap-2 mb-3">
            <input type="hidden" name="type_produk" id="typeProduk" value="{{ !isset($session) ? 1 : ($session['type_produk'] == 1 ? 1 : 2) }}">
          <button type="button" {{ !isset($session) ? '' : ($session['step'] == 2 ? 'disabled' : '') }} id="produkAuto" onclick="handleTypeProduk(1)"
            class="w-1/2 border {{ !isset($session) ? '' : ($session['step'] == 2 ? 'opacity-65 hover:cursor-not-allowed' : '') }} {{ !isset($session) ? '' : ($session['type_produk'] == 1 ? 'bg-blue-500 text-slate-50' : '') }} hover:bg-blue-500 hover:text-slate-50 rounded-lg px-3 py-2 font-bold shadow-lg">
            Auto
          </button>
          <button type="button" {{ !isset($session) ? '' : ($session['step'] == 2 ? 'disabled' : '') }} id="produkManual" onclick="handleTypeProduk(2)"
            class="w-1/2 border {{ !isset($session) ? '' : ($session['step'] == 2 ? 'opacity-65 hover:cursor-not-allowed' : '') }} {{ !isset($session) ? '' : ($session['type_produk'] == 2 ? 'bg-blue-500 text-slate-50' : '') }} hover:bg-blue-500 hover:text-slate-50 rounded-lg px-3 py-2 font-bold shadow-lg">
            Manual
          </button>
        </div>
        
        @if(session()->has('produkTemp'))
            @php
                $sessionProduk = session()->get('produkTemp');
            @endphp
            @if($sessionProduk['step'] == 2)
                <div class="rounded-lg px-2 py-3 bg-white shadow-lg">
                    <div class="py-2 flex items-center">
                        <span class="font-semibold me-3">Terapkan Affiliate</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input onclick="terapkanAffiliasi(this)" @error('status_affiliasi') checked @enderror name="status_affiliasi" type="checkbox" class="sr-only peer">
                            <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div @error('status_affiliasi') style="display: block" @else style="display: none" @enderror id="groupAffiliasi">
                        <span class="text-red-500 text-[0.8em] block">Perhitungan Komisi Affiliate Berdasarkan Persen</span>
                        <div class="relative overflow-hidden w-full">
                            <label for="komisiAffiliasi" class="text-[0.7em] absolute top-2 left-4 text-blue-600">Total Komisi</label>
                            <select name="komisi_affiliasi" id="komisiAffiliasi" class="w-full @error('komisi_affiliasi') border-red-500 @enderror overflow-hidden border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
                                <option value="">-- Pilih Jumlah Komisi --</option>
                                <option value="1">1%</option>
                                <option value="2">2%</option>
                                <option value="3">3%</option>
                                <option value="4">4%</option>
                                <option value="5">5%</option>
                                <option value="6">6%</option>
                                <option value="7">7%</option>
                                <option value="8">8%</option>
                                <option value="9">9%</option>
                                <option value="10">max: 10%</option>
                            </select>
                        </div>
                    </div>
                    @error('komisi_affiliasi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <div class="py-2 flex items-center">
                        <span class="font-semibold me-3">Terapkan Garansi</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input onclick="terapkanGaransi(this)" name="status_garansi" @error('status_garansi') checked @enderror type="checkbox" class="sr-only peer">
                            <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div id="groupGaransi" @error('status_affiliasi') style="display: block" @else style="display: none" @enderror>
                        <span class="text-red-500 text-[0.8em] block">Perhitungan Lama Garansi Berdasarkan Hari</span>
                        <div class="relative overflow-hidden w-full">
                            <label for="garansi" class="text-[0.7em] absolute top-2 left-4 text-blue-600">Lama Garansi</label>
                            <select name="garansi" id="garansi" class="w-full border rounded-lg @error('garansi') border-red-500 @enderror focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="1">1 Hari</option>
                                <option value="2">2 Hari</option>
                                <option value="3">3 Hari</option>
                                <option value="4">4 Hari</option>
                                <option value="5">5 Hari</option>
                                <option value="6">6 Hari</option>
                                <option value="7">7 Hari</option>
                            </select>
                        </div>
                    </div>
                    @error('garansi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    @if($sessionProduk['type_produk'] == 2)
                        <div class="relative mt-3 overflow-hidden w-full">
                            <label for="waktuProses" class="text-[0.7em] absolute top-2 left-4 text-blue-600">Waktu Proses</label>
                            <select id="waktuProses" required name="waktu_proses" class="w-full @error('waktu_proses') border-red-500 @enderror border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
                                <option value="">-- Pilih Waktu Proses --</option>
                                <option value="1">1 Hari</option>
                                <option value="2">2 Hari</option>
                                <option value="3">3 Hari</option>
                                <option value="4">4 Hari</option>
                                <option value="5">5 Hari</option>
                                <option value="6">6 Hari</option>
                                <option value="7">7 Hari</option>
                            </select>
                        </div>
                        @error('waktu_proses')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    @endif
                    <div class="py-2 mt-3">
                        <span class="font-semibold">Deskripsi Produk</span>
                    </div>
                    <div class="mb-3">
                        <textarea name="deskripsi" id="deskripsi" required>{{ old('deskripsi') }}</textarea>
                    </div>
                    @error('deskripsi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <div class="py-2">
                        <span class="font-semibold">Upload File</span>
                    </div>
                    @if($sessionProduk['type_produk'] == 1)
                        <div class="border mt-3 bg-slate-100 rounded-lg h-[150px] flex justify-center items-center relative">
                            {{-- <div class="w-full h-full flex justify-center relative flex-col items-center">
                                <i class="ri-folder-upload-fill text-slate-400 text-[3em]"></i>
                                <span class="text-sm block text-slate-400"><i class="ri-upload-fill"></i> Upload File Kamu Disini</span>
                                <span v-if="form.file.file_name != ''" class="text-sm block text-slate-400 text-center">Name : {{ (form.file.file_name != '' ? form.file.file_name : 'Upload File Kamu Disini') }}</span>
                                <span v-if="form.file.size > 0" class="text-sm text-slate-400 text-center block">Size : {{ (form.file.size) }}MB</span>
                                <input accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed,.text,.pdf" type="file" class="absolute top-0 left-0 bottom-0 right-0 opacity-0 z-40">
                            </div> --}}
                            <div class="flex items-center justify-center w-full">
                                <label for="fileUpload" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik atau</span> seret file disini</p>
                                        {{-- <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p> --}}
                                    </div>
                                    <input id="fileUpload" name="file_produk" type="file" class="hidden" />
                                </label>
                            </div> 
                        </div>
                    @endif
                    <div class="py-2">
                        <div class="py-2">
                            <span class="font-semibold">Data Form</span>
                        </div>
                        {{-- <div class="flex flex-col gap-4 items-center border rounded-lg py-3 mb-3 justify-center">
                            <span class="text-sm">{{ showForm ? 'Tambahkan Form Data Pembeli' : 'Apakah Anda Membutuhkan Data Pembeli?' }}</span>
                            <div v-if="!showForm" class="flex items-center gap-4 py-4">
                            <button type="button" @click="showForm = true" class="px-4 py-2 bg-blue-600 text-slate-50 rounded-md text-xs">Ya</button>
                            <button type="button" @click="showForm = true" class="px-4 py-2 bg-red-500 text-slate-50 rounded-md text-xs">Tidak</button>
                            </div>
                            <div v-else>
                            <div class="">
                                <button @click="handleAddForm" class="px-4 py-2 border-blue-600 border rounded-lg text-blue-500 text-xs">Tambah Form</button>
                            </div>
                            </div>
                        </div> --}}
                        {{-- <div class="mt-3" v-if="showAddForm && !selesaiAdd">
                            <div class="mb-4 relative overflow-hidden w-full">
                            <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Nama Form</label>
                            <input ref="dataForm" v-model="formDataPembeli.nama" type="text" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm" placeholder="cth : Url Website">
                            </div>
                            <div class="mb-4 relative overflow-hidden w-full">
                            <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Type Form</label>
                            <select v-model="formDataPembeli.type" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
                                <option value="text">Free Text</option>
                            </select>
                            </div>
                            <button @click="addForm" type="button" class="px-4 py-2 bg-blue-600 text-slate-50 rounded-md text-xs"><i class="ri-add-fill"></i> Tambah</button>
                        </div> --}}
                        {{-- <ul v-if="showAddForm" class="mt-2">
                            <li class="border px-3 py-2 relative justify-between flex items-center" v-for="(list, index) in listFormDataPembeli" :key="index">
                            <span class="text-xs">{{ list.name }}</span>
                            <button @click="deleteForm(index)" class="text-sm text-red-500 hover:text-red-400" title="Hapus Form">
                                <i class="ri-close-circle-fill"></i>
                            </button>
                            </li>
                        </ul> --}}
                        </div>
                        <div class="py-4 flex items-center lg:flex-row flex-col gap-3">
                        <button class="px-6 py-3 bg-blue-600 text-slate-50 rounded-md w-full">{{ 'Upload Produk' }}</button>
                        {{-- <button v-else type="button" :disabled="is_loading" @click="handleSubmit" class="px-6 text-center py-3 bg-blue-600 rounded-md text-slate-50 w-full">
                            <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                            </svg>
                            Loading
                        </button> --}}
                        <a href="{{ route('toko.daftar-produk') }}" class="w-full bg-red-500 px-6 text-center py-3 text-slate-50 rounded-md">Batal</a>
                    </div>
                </div>
            @else
                <div class="rounded-lg px-2 py-3 bg-white shadow-lg">
                    <div class="py-2">
                        <span class="font-semibold">Detail Produk</span>
                    </div>
                    <div class="relative overflow-hidden w-full">
                        <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Nama Produk</label>
                        <input required value="{{ old('nama_produk') }}" name="nama_produk" type="text" class="w-full border rounded-lg @error('nama_produk') border-red-500 @enderror focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm" placeholder="Nama Produk">
                    </div>
                    @error('nama_produk')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <div class="relative mt-3 overflow-hidden w-full">
                        <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Kategori Produk</label>
                        <select required name="kategori" class="w-full border rounded-lg @error('kategori') border-red-500 @enderror focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach(App\Models\Kategori::where('an', 1)->get() as $key => $value)
                                <option {{ old('kategori') == $value->kode_kategori ? 'selected' : '' }} value="{{ $value->kode_kategori }}">{{ $value->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('kategori')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <div class="py-2 flex items-center">
                        <span class="font-semibold me-3">Terapkan Diskon</span>
                        <label class="relative inline-flex items-center cursor-pointer me-2">
                            <input onclick="terapkanDiskon(this)" @error('potongan_persen') checked @enderror @error('potongan_harga') checked @enderror name="status_diskon" type="checkbox" class="sr-only peer">
                            <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                        {{-- <Popper content="Atur diskon produk berdasarkan persen atau harga.">
                            <button><i class="ri-information-fill hover:cursor-pointer hover:text-blue-600"></i></button>
                        </Popper> --}}
                    </div>
                    
                    <div @error('potongan_persen') style="display: block" @else @error('potongan_harag') style="display: block" @else style="display: none" @enderror @enderror id="groupDiskon">
                        <ul class="flex text-sm mb-3 rounded-ss-xl rounded-es-xl rounded-ee-xl rounded-se-xl overflow-hidden w-max">
                            <li id="diskonPersen" class="px-3 @error('potongan_persen') bg-blue-600 text-slate-50 @enderror py-2 border hover:text-slate-50 hover:bg-blue-600 hover:cursor-pointer">Persen %</li>
                            <li id="diskonHarga" class="px-3 py-2 @error('potongan_harga') bg-blue-600 text-slate-50 @enderror border hover:text-slate-50 hover:bg-blue-600 hover:cursor-pointer">Harga Rp.</li>
                        </ul>
                        <input type="hidden" id="typePotongan" name="type_potongan">
                        <div id="inputPotonganPersen" @error('potongan_persen') style="display: block" @else style="display: none" @enderror class="mt-3 relative overflow-hidden w-full">
                            <label class="text-[0.7em] absolute top-2 left-4 @error('potongan_persen') border-red-500 @enderror text-blue-600">Potongan Persen</label>
                            <input id="potonganPersen" name="potongan_persen" type="number" min="1" max="100" class="w-full border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm" placeholder="cth : 50">
                        </div>
                        @error('potongan_persen')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <div id="inputPotonganHarga" @error('potongan_harga') style="display: block" @else style="display: none" @enderror class="mt-3 relative overflow-hidden w-full">
                            <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Potongan Harga</label>
                            <input id="potonganHarga" type="number" name="potongan_harga" min="3000" class="w-full @error('potongan_harga') border-red-500 @enderror border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm" placeholder="cth : 20000">
                        </div>
                        @error('potongan_harga')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="relative mt-3 overflow-hidden w-full">
                        <label class="text-[0.7em] absolute top-2 left-4 text-blue-600">Harga</label>
                        <input value="{{ old('harga') }}" name="harga" min="5000" placeholder="cth : 200000" type="number" class="w-full @error('harga') border-red-500 @enderror border rounded-lg focus:border-red-500 hover:bg-slate-200 h-full p-4 pt-6 focus:outline-none text-sm">
                    </div>
                    @error('harga')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <div class="py-2 mt-3">
                        <span class="font-semibold">Upload Gambar</span>
                    </div>
                    <div id="wrapperImage" class="relative overflow-hidden bg-slate-100 flex justify-start flex-wrap p-2 w-full min-h-[180px] max-h-max border rounded-lg">
                        <div class="flex items-center flex-wrap" id="wrapperPreviewImages">

                        </div>
                        <div id="defaultCard" class="min-h-[120px] w-full lg:min-h-[130px] p-2 lg:w-[25%]">
                            <div class="bg-slate-300 w-full h-full rounded-lg hover:cursor-pointer hover:bg-slate-200 flex justify-center items-center relative">
                                <i class="ri-add-fill text-[2em] text-slate-400"></i>
                                <input type="file" id="inputImage" accept="image/*" class="absolute hover:cursor-pointer z-50 opacity-0 top-0 left-0 bottom-0 right-0">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-slate-50 rounded-md w-full">Lanjutkan</button>
                    {{-- <div class="py-4 flex items-center lg:flex-row flex-col gap-3">
                    <button v-if="!is_loading" @click="handleSubmit" class="px-6 py-3 bg-blue-600 text-slate-50 rounded-md w-full">{{ step == 1 ? 'Selanjutnya' : 'Upload Produk' }}</button>
                    <button v-else type="button" :disabled="is_loading" @click="handleSubmit" class="px-6 text-center py-3 bg-blue-600 rounded-md text-slate-50 w-full">
                        <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                        </svg>
                        Loading
                    </button>
                    <a href="" class="w-full bg-red-500 px-6 text-center py-3 text-slate-50 rounded-md">Batal</a>
                    </div> --}}
                </div>
            @endif
        @endif
    </form>
</div>

{!! renderScript('script-upload-produk') !!}


