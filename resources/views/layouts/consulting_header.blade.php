@include('layouts.page_nav')
<div class="generic-image-banner-container">
    <div class="generic-image-banner">
        <img src="{{ asset('/img/consultants/consulting_banner.jpeg') }}" alt="consulting landing page"
            class="generic-banner-image" loading="lazy">

        <div class="overlay generic-text-container">
            <div class="container generic-management">
                <h2 class="mb-3 generic-title">{{ $pageContent->title }}
                </h2>
            </div>
        </div>
    </div>
</div>
