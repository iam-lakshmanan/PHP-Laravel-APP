@extends('layouts.team_page_home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.page_nav')
@include('layouts.team_header')


@php
if (
count($getTeamTestinomial) > 0 ||
count($teamLinksPage) > 0 ||
$specialtiesIndustry->count() > 0 ||
$specialties->count() > 0 ||
count($teamVideoTestimonials) > 0
) {
$col_md_data1 = 'col-md-7';
$col_md_data2 = 'col-md-4';
$justify_content = 'justify-content-end';
$consultants_courses_content_aside = '';
} else {
$col_md_data1 = 'col-md-8';
$col_md_data2 = '';
$justify_content = 'justify-content-center';
$consultants_courses_content_aside = 'consultants_courses_content_aside';
}
@endphp

<section id="section-team-page">
    <div class="container-fluid">
        <div class="row d-flex {{ $justify_content }} gap-5 mt-2 upcoming-courses-list-color">
            <div class="upcoming-courses-list col-12 {{ $col_md_data1 }}">
                <div class="consultants-courses-content mb-5 {{ $consultants_courses_content_aside }}">
                    @php
                    $imageUrl = null;
                    if (!empty($getTeamPage->uri)) {
                    $pathParts = explode(config('app_constants.PUBLIC_FOLDER'), $getTeamPage->uri);
                    $relativePath = isset($pathParts[1]) ? $pathParts[1] : $getTeamPage->filename;
                    $imageUrl = env('TEAM_CONSULTANTS_FILE_FOLDER') . '/' . $relativePath;
                    }
                    @endphp

                    @if ($imageUrl)
                    <aside>
                        <img src="{{ asset($imageUrl) }}" alt="{{ @$getTeamPage->title }}" class="img-fluid team-image" loading="lazy">
                    </aside>

                    @endif

                    <p id="team-content"></p>


                </div>
            </div>

            @if (count($getTeamTestinomial) > 0 || count($teamLinksPage) > 0 ||$specialtiesIndustry->count() > 0 ||
            $specialties->count() > 0 || count($teamVideoTestimonials)>0
            )
            <div class="upcoming-courses-list-fullleft col-12 {{ @$col_md_data2 }} mt-0 p-0">
                @if (count($getTeamTestinomial) > 0)
                <div class="consultants-courses-content-left">
                    <div id="testimonial_carousel" class="testimonial-carousel">
                        <div class="container testimonial-container">
                            <h6 class="upcoming-courses-testimonials-title list">TESTIMONIALS</h6>
                            <ul class="upcoming-testimonial-list">
                                @foreach ($getTeamTestinomial as $testimonial)
                                <li>
                                    <div class="upcoming-testimonial-content">
                                        <div class="row">
                                            <div class="col-12 px-0">
                                                <span class="testimonial-content list-upcoming" data-testimonial-id="{{ $testimonial->id }}">
                                                    @php
                                                    $bodyText =
                                                    strip_tags($testimonial->FieldDataBodyCourse->body_value);
                                                    $shortText = substr($bodyText, 0, 185);
                                                    $isLongText = strlen($bodyText) > 185;
                                                    @endphp

                                                    @if ($isLongText)
                                                    <p class="short-text">
                                                        "{!! $shortText !!}..."
                                                        <a href="#" class="consultant-testimonial-toggle" data-action="more">See more</a>
                                                    </p>
                                                    <p class="full-text hidden">
                                                        "{!! $bodyText !!}"
                                                        <a href="#" class="consultant-testimonial-toggle" data-action="less">See less</a>
                                                    </p>
                                                    @else
                                                    <p>"{!! $bodyText !!}"</p>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-3 testimonials-images">
                                                @php
                                                $testimonialImage =
                                                @$testimonial->FieldDataFieldTestimonialImage->FileManagedTestimonialImage->uri;
                                                if ($testimonialImage) {
                                                $pathParts = explode(
                                                config('app_constants.PUBLIC_FOLDER'),
                                                $testimonialImage
                                                );
                                                $relativePath = isset($pathParts[1]) ? $pathParts[1] :
                                                $testimonialImage;
                                                $imageUrl = env('FILE_FOLDER') . '/' . $relativePath;
                                                } else {
                                                $imageUrl = asset('/img/upcoming_public_courses/user.jpg');
                                                }
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="Testimonial Image" class="img-fluid testimonial-img list-upcoming" loading="lazy">
                                            </div>
                                            <div class="col-9 testimonials-text-content">
                                                <p class="testimoinals-title-text list-upcoming">
                                                    {{ @$testimonial->field_author_value }}
                                                </p>
                                                <p class="position-text list-upcoming">
                                                    {{ @$testimonial->FieldDataFieldTestimonialPositionNode->field_testimonial_position_value  }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @if($teamtestimonialcount > 10)
                            @php
                            $encryptedNodeId = Crypt::encryptString($testimonial->field_team_target_target_id);
                            @endphp
                            <a href="{{ route('consultants.testimonials', ['nodeId' => $encryptedNodeId]) }}" class="view-testimonial-link  view-all-content">View All</a>
                            @endif

                            <div class="dots-container test-testimonials"></div>
                        </div>
                    </div>
                </div>
                @endif
                @if(isset($teamLinksPage) && count($teamLinksPage) > 0)
                <div class="team-page-leftcontent-info">
                    <div class="team-page-links-container mt-5">
                        @if(isset($teamLinksPage) && count($teamLinksPage) > 0)
                        <h6 class="team-page-links-title ms-1">LINKS</h6>
                        @foreach ($teamLinksPage as $item_links)
                        <div class="team-link-item">
                            @if (@$item_links->field_links_title == 'LinkedIn')
                            <a href="{{ @$item_links->field_links_url }}" target="_blank">
                                <img src="{{ asset('/img/header_image/social_media_linkedIn_icon.png') }}" alt="LinkedIn Icon" class="ms-1" loading="lazy">
                            </a>
                            @elseif(@$item_links->field_links_title == 'Download CV')
                            <a href="{{ @$item_links->field_links_url }}" target="_blank">
                                <span class="d-inline-block ms-1" style="width: 33px; display: block;"></span>
                            </a>
                            @else
                            <a href="{{ @$item_links->field_links_url }}" target="_blank">
                                <img src="{{ asset('/img/header_image/social_media_RSS_icon.png') }}" alt="RSS Icon" class="ms-1" loading="lazy">
                            </a>
                            @endif
                            <p class="team-links-url mb-2">
                                <a href="{{ @$item_links->field_links_url }}" target="_blank">
                                    {{ @$item_links->field_links_title }}
                                </a>
                            </p>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    @endif

                    @if(($specialtiesIndustry && $specialtiesIndustry->count()) || ($specialties && $specialties->count()))
                    <section class="specialties" style="{{ !isset($teamLinksPage) || count($teamLinksPage) === 0 ? 'margin: 0px; margin-top: 40px;' : '' }}">
                        <div class="specialties-header">
                            <div class="specialties-title">
                                <h5>SPECIALTIES</h5>
                            </div>
                            <div class="consultant-specialist">
                                <div class="container">
                                    @if($specialtiesIndustry && $specialtiesIndustry->count())
                                    <h4>{{ $specialtiesIndustry->first()->vocabulary_name }}</h4>
                                    <p>{{ $specialtiesIndustry->pluck('name')->implode(', ') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="consultant-specialist-areas">
                                <div class="container">
                                    @if($specialties && $specialties->count())
                                    <h4>{{ $specialties->first()->vocabulary_name }}</h4>
                                    <p>{{ $specialties->pluck('name')->implode(', ') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>
                    @endif
                    <input type="hidden" name="team_page_nid" id="team_page_nid" value="{{ @$getTeamPage->nid }}">
                    @if(count($teamVideoTestimonials)>0)
                    <section class="consultant-testimonial">
                        <h3 class="team-videotest-info-title">VIDEOS</h3>
                        <div id="result">
                            <span id="loader" class="loader"></span>
                        </div>
                    </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@include('layouts.footer')
@endsection
