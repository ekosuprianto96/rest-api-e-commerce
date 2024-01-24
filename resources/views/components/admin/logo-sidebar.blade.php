<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon">
        @if($image) 
            <img width="40" src="{{ $image }}" alt="{{ $alt }}">
        @else
            <i class="fas fa-laugh-wink"></i>
        @endif
    </div>
    <div class="sidebar-brand-text mx-3 text-nowrap">{{ $logoText }}</div>
</a>