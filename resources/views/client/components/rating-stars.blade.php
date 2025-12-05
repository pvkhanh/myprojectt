@php
    $rounded = round($rating);
@endphp

<div class="rating d-flex align-items-center">
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= $rounded)
            <i class="bi bi-star-fill text-warning"></i>
        @else
            <i class="bi bi-star text-warning"></i>
        @endif
    @endfor

    @if (isset($count))
        <span class="ms-2 text-muted" style="font-size: 14px">
            ({{ $count }} đánh giá)
        </span>
    @endif
</div>
