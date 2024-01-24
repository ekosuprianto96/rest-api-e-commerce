<div>
    <div class="min-w-full max-w-screen border lg:h-[350px] h-max items-center">
        <div class="w-full max-w-screen h-full flex gap-2 items-center overflow-hidden p-2">
            @foreach($banners->where('an', 1)->get() as $key => $value)
                <div class="min-w-full overflow-hidden h-full flex border justify-center items-center bg-slate-400 rounded-lg">
                    <img src="{{ $value->image }}" class="w-full" alt="">
                </div>                
            @endforeach
        </div>
    </div>
</div>