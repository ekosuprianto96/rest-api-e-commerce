@extends('frontend.layouts.index', ['title' => 'Profile'])

@section('content')
    <x-frontend.layouts.container>
        <div class="w-full h-max py-4">
            <div class="grid grid-cols-12 relative w-full h-max gap-3">
                <div class="lg:col-span-3 lg:block hidden sticky top-0 w-full h-full border p-3 shadow-lg bg-white rounded-lg overflow-x-hidden">
                    @include('frontend.layouts.menuSidebarUser')
                </div>
                <div class="lg:col-span-9 col-span-12">
                    <div class="rounded-lg px-2 py-3 shadow-lg bg-white">
                        <div class="px-2">
                            @include('frontend.dashboard.contents.content-profile', ['title' => 'Profile', 'account' => $user])
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </x-frontend.layouts.container>
@endsection