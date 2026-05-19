@if(isset($breadcrumbs) && count($breadcrumbs))
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        @foreach($breadcrumbs as $crumb)
            @if(isset($crumb['url']))
                <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
            @else
                <li class="breadcrumb-item active">{{ $crumb['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
