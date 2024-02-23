@extends('frontend.layouts.index', ['title' => 'Dasbord Toko'])

@section('content')
    <x-frontend.layouts.container>
        <div class="w-full py-4">
            <div class="grid grid-cols-12 h-full relative w-full gap-3">
                <div class="lg:col-span-3 max-h-max h-max relative lg:block hidden top-0 w-full border p-3 shadow-lg bg-white rounded-lg overflow-x-hidden overflow-y-hidden">
                    @include('frontend.layouts.menuSidebarToko')
                </div>
                <div class="lg:col-span-9 col-span-12">
                    <div class="rounded-lg px-2 h-full py-3 shadow-lg bg-white">
                        <div class="px-2">
                            @include('frontend.toko.contents.content-dashboard', ['title' => 'Dasbord Toko'])
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </x-frontend.layouts.container>
@endsection