@if($isMobile)
<div class="border top-0 bottom-14 left-0 right-0 fixed transition-all px-2 scroll-custom overflow-y-auto bg-white z-[70]">
    <div class="py-3">
      <button>
        <i class="ri-arrow-left-line"></i> Close
      </button>
    </div>
    <span class="my-4 block">Pilih Bank</span>
    <ul>
        {{-- shadow-lg bg-blue-400 text-slate-50 --}}
      <li class="border relative flex items-center hover:cursor-pointer hover:shadow-lg hover:bg-blue-400 hover:text-slate-50 p-3 mb-2 rounded-lg">
        <img width="50">
        <span class="block ms-2 font-bold">{{ '-' }}</span>
        <input class="absolute right-6" type="radio">
      </li>
    </ul>
</div>
@else
<ul class="w-full mt-3 pl-3">
    <li class="border relative flex items-center hover:cursor-pointer hover:shadow-lg hover:bg-blue-400 hover:text-slate-50 p-3 mb-2 rounded-lg">
      <img width="30">
      <span class="block ms-2 font-bold">{{ '-' }}</span>
      <input class="absolute right-6" type="radio">
    </li>
</ul>
@endif