<div class="product-gallery">

    {{-- Ảnh chính --}}
    <div class="main-image mb-3">
        <img id="mainImage" src="{{ $product->main_image }}" class="img-fluid rounded border w-100" style="cursor: zoom-in"
            data-bs-toggle="modal" data-bs-target="#zoomImageModal">
    </div>

    {{-- Thumbnail --}}
    <div class="row g-2">
        @foreach ($product->gallery_images as $img)
            <div class="col-3">
                <img src="{{ $img }}" class="img-thumbnail gallery-thumb" style="cursor:pointer"
                    onclick="document.getElementById('mainImage').src='{{ $img }}'">
            </div>
        @endforeach
    </div>

    {{-- Modal zoom --}}
    <div class="modal fade" id="zoomImageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <img src="{{ $product->main_image }}" id="zoomImage" class="img-fluid rounded">
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        // Update zoom modal image when main image changes
        document.getElementById('mainImage').addEventListener('load', function() {
            document.getElementById('zoomImage').src = this.src;
        });
    </script>
@endpush
