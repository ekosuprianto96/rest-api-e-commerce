@php
    $user = Auth::user();
@endphp
<div class="py-3">
    <h4 class="font-bold">{{ $title }}</h4>

    <div class="mt-4">
        {!! $chart->container() !!}
    </div>

    <div>
        <h4 class="mb-3">Daftar Order Hari Ini</h4>
        <div class="overflow-y-auto h-max overflow-hidden rounded-lg">
            <table id="tableOrder" class="display w-full">
                <thead class="bg-blue-300 text-slate-50">
                    <tr>
                        <th class="text-xs p-3">#</th>
                        <th class="text-xs p-3">No Order</th>
                        <th class="text-xs p-3">Nama Produk</th>
                        <th class="text-xs p-3">Total Dibayar</th>
                        <th class="text-xs p-3">Nama Pembeli</th>
                        <th class="text-xs p-3">Tanggal</th>
                        <th class="text-xs p-3">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-slate-100">
                    <tr class="hover:bg-slate-200">
                        <td class="text-xs text-center p-3">1</td>
                        <td class="text-xs text-center p-3">TRX54326436</td>
                        <td class="text-xs text-center p-3">Produk Testing</td>
                        <td class="text-xs text-center p-3">Rp. 150.000</td>
                        <td class="text-xs text-center p-3">Eko Suprianto</td>
                        <td class="text-xs text-center p-3">02 Feb 2024</td>
                        <td class="text-xs text-center p-3">Lihat</td>
                    </tr>
                    <tr class="hover:bg-slate-200">
                        <td class="text-xs text-center p-3">1</td>
                        <td class="text-xs text-center p-3">TRX54326436</td>
                        <td class="text-xs text-center p-3">Produk Testing</td>
                        <td class="text-xs text-center p-3">Rp. 150.000</td>
                        <td class="text-xs text-center p-3">Eko Suprianto</td>
                        <td class="text-xs text-center p-3">02 Feb 2024</td>
                        <td class="text-xs text-center p-3">Lihat</td>
                    </tr>
                    <tr class="hover:bg-slate-200">
                        <td class="text-xs text-center p-3">1</td>
                        <td class="text-xs text-center p-3">TRX54326436</td>
                        <td class="text-xs text-center p-3">Produk Testing</td>
                        <td class="text-xs text-center p-3">Rp. 150.000</td>
                        <td class="text-xs text-center p-3">Eko Suprianto</td>
                        <td class="text-xs text-center p-3">02 Feb 2024</td>
                        <td class="text-xs text-center p-3">Lihat</td>
                    </tr>
                    <tr class="hover:bg-slate-200">
                        <td class="text-xs text-center p-3">1</td>
                        <td class="text-xs text-center p-3">TRX54326436</td>
                        <td class="text-xs text-center p-3">Produk Testing</td>
                        <td class="text-xs text-center p-3">Rp. 150.000</td>
                        <td class="text-xs text-center p-3">Eko Suprianto</td>
                        <td class="text-xs text-center p-3">02 Feb 2024</td>
                        <td class="text-xs text-center p-3">Lihat</td>
                    </tr>
                    <tr class="hover:bg-slate-200">
                        <td class="text-xs text-center p-3">1</td>
                        <td class="text-xs text-center p-3">TRX54326436</td>
                        <td class="text-xs text-center p-3">Produk Testing</td>
                        <td class="text-xs text-center p-3">Rp. 150.000</td>
                        <td class="text-xs text-center p-3">Eko Suprianto</td>
                        <td class="text-xs text-center p-3">02 Feb 2024</td>
                        <td class="text-xs text-center p-3">Lihat</td>
                    </tr>
                    <tr class="hover:bg-slate-200">
                        <td class="text-xs text-center p-3">1</td>
                        <td class="text-xs text-center p-3">TRX54326436</td>
                        <td class="text-xs text-center p-3">Produk Testing</td>
                        <td class="text-xs text-center p-3">Rp. 150.000</td>
                        <td class="text-xs text-center p-3">Eko Suprianto</td>
                        <td class="text-xs text-center p-3">02 Feb 2024</td>
                        <td class="text-xs text-center p-3">Lihat</td>
                    </tr>
                    <tr class="hover:bg-slate-200">
                        <td class="text-xs text-center p-3">1</td>
                        <td class="text-xs text-center p-3">TRX54326436</td>
                        <td class="text-xs text-center p-3">Produk Testing</td>
                        <td class="text-xs text-center p-3">Rp. 150.000</td>
                        <td class="text-xs text-center p-3">Eko Suprianto</td>
                        <td class="text-xs text-center p-3">02 Feb 2024</td>
                        <td class="text-xs text-center p-3">Lihat</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ $chart->cdn() }}"></script>
{{ $chart->script() }}

