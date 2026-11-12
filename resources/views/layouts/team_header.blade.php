<div class="individualcourse-image-banner-container">
    <div class="individualcourse-image-banner">
        <img src="{{ asset('/img/consultants/our_consultants.png') }}" alt="{{ $getTeamPage->title }}"
            class="individualcourse-banner-image" loading="lazy">
        <div class="overlay individualcourse-text-container">
            <div class="container teamcourse-management">
                <h2 class="mb-3 teamheader-title mt-2"
                    style="{{ empty($getTeamPage->field_credentials_value) ? 'margin: 0px;' : '' }}">
                    {{ $getTeamPage->title }}
                </h2>
                @if(!empty($getTeamPage->field_credentials_value))
                    <p class="teamheader-credentials">{{ $getTeamPage->field_credentials_value }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
