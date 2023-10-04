<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon rotate-n-15">
        @if($image) 
            <img src="{{ $image }}" alt="{{ $alt }}">
        @else
            <i class="fas fa-laugh-wink"></i>
        @endif
    </div>
    <div class="sidebar-brand-text mx-3">{{ $logoText }}</div>
</a>