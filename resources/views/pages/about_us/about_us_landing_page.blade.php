@extends('layouts.aboutus_page_home')

@section('content')

@include('layouts.page_nav')
@include('layouts.aboutus_header')

<section class="about-management-block">
    <div class="container py-3">
        <div class="row">
            <div class="col-xs-12 col-sm-12  col-md-0 col-lg-0 col-xl-1"></div>
            <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 col-xl-5 mt-4 py-2">
                <div class="aboutus-management-img text-center mb-4">
                    <a href="{{route('managementteam.show')}}">
                        <img src="{{asset('/img/about_us/management_team_icon.png')}}" alt="consulting-icon"
                            width="89"></a>
                </div>
                <div class="aboutus-title">
                    <a href="{{ route('managementteam.show') }}">
                        <h6 class="text-center aboutus-title-color">MANAGEMENT TEAM</h6>
                    </a>
                </div>
                <div class="aboutus-management-content text-center">
                    <span>A group of seasoned professionals,</span><br>
                    <span>providing vision and strategy, leading our</span><br>
                    <span>team to help our clients improve their</span><br>
                    <span>project delivery excellence.</span>
                </div>
                <div class="aboutus-management-button text-center py-3 mb-2">
                    <a href="{{ route('managementteam.show') }}"> <img
                            src="{{asset('/img/about_us/learn_more_button.png')}}" alt="Management Team" width="148"></a>
                </div>


            </div>
            <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 col-xl-5 mt-4 py-2">
                <div class="aboutus-consultants-img text-center mb-4">
                    <a
                        href="{{ route('our.consultants.page', ['consultant_tab' => config('app_constants.OUR_CONSULTANTS_SLUG')]) }}">
                        <img src="{{asset('/img/about_us/trainers_consultants_icon.png')}}" alt="Consultants & Trainers" width="89"></a>
                </div>
                <div class="aboutus-title"> <a
                        href="{{ route('our.consultants.page', ['consultant_tab' => config('app_constants.OUR_CONSULTANTS_SLUG')]) }}">
                        <h6 class="text-center aboutus-title-color">CONSULTANTS & TRAINERS</h6>
                    </a>
                </div>
                <div class="aboutus-consultants-content text-center">
                    <span>Award-winning professionals with extensive</span><br>
                    <span>industry experience who help client organizations</span><br>
                    <span>maximize results by growing capabilities, reducing</span><br>
                    <span>risk, and improving performance.</span><br>

                </div>
                <div class="aboutus-consultants-button text-center py-3 mb-2">
                    <a
                        href="{{ route('our.consultants.page', ['consultant_tab' => config('app_constants.OUR_CONSULTANTS_SLUG')]) }}">
                        <img src="{{asset('/img/about_us/meet_the_team_button.png')}}" alt="Consultants & Trainers" width="173"></a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12  col-md-0 col-lg-0  col-xl-1"></div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row mt-5">
            <div class="col-xs-12 col-sm-12  col-md-0 col-lg-0 col-xl-1"></div>
            <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 col-xl-5 aboutus-industry">
                <div class="aboutindustry-list aboutus-title">
                    <h6>INDUSTRY RECOGNITION</h6>
                </div>
                <div class="aboutindustry-text-content h-50">
                    <span>Procept, it's subsidiaries, its courses, and it's consultants</span><br>
                    <span>and trainers, have won numerous industry awards,</span><br>
                    <span>recognizing thought leadership, innovation, and excellence.</span><br>
                    <a href="{{ route('awards.page') }}" class="aboutindustry-details-btn">Awards ></a>
                </div>
                @if (count($industryRecLogoslider)>0)
                <div class="aboutindustry-slider-wrapper position-relative">
                    <div class="aboutindustry-slider mx-4">
                        @php
                        $loopCount = 10;
                        @endphp

                        @for ($i = 0; $i < $loopCount; $i++) @foreach ($industryRecLogoslider as $item) <p
                            class="industry-slide">

                            @php
                            $industryImage=$item->uri;

                            if ($industryImage) {
                            $pathParts = explode(
                            config('app_constants.PUBLIC_FOLDER'),
                            $industryImage
                            );
                            $relativePath = isset($pathParts[1]) ? $pathParts[1] :
                            $industryImage;

                           $imageUrl = env('INDUSTRY_RECO_FILE_FOLDER') . $relativePath;
                            }

                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ @$relativePath }}">
                            </p>

                            @endforeach
                            @endfor
                    </div>

                    <div class="carousel-button aboutus-carousel-button-right">
                        <span class="fa fa-angle-right industry-right-btn"></span>

                    </div>
                    <div class="carousel-button aboutus-carousel-button-left">
                        <span class="fa fa-angle-left industry-left-btn"></span>

                    </div>
                </div>
                @endif
            </div>

            <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 col-xl-5 about-partnership-full-content">
                <div class="aboutpartnership-list aboutus-title">
                    <h6>PARTNERSHIPS</h6>
                </div>
                <div class="aboutpartnership-text-content h-50">
                    <span>Procept supplies the courses and instructors behind</span><br>
                    <span>continuing education or professional development</span><br>
                    <span>programs for over 18 colleges, universities, professional</span><br>
                    <span>associations, and regional innovation centers.</span><br>
                    <a href="{{ route('earningcredits.show') }}" class="aboutpartnership-details-btn d-block mb-3">Accreditation ></a>
                </div>
                @if (count($partnersLogoslider)>0)
                <div class="partnerslogo-slider-wrapper position-relative">
                    <div class="partnerslogo-slider mx-4">
                        @php
                        $loopCount = 10;
                        @endphp
                        @for ($i = 0; $i < $loopCount; $i++) @foreach ($partnersLogoslider as $item) @php
                            $fullUrl=$item['image']; $baseUrl=dirname($fullUrl) . '/' ; $fileName=basename($fullUrl);
                            @endphp <p class="partners-slide">
                            @if (isset($item['image']))
                            @if (env('FILE_FOLDER') == $baseUrl)
                            <img src="{{ $item['image'] }}" alt="{{ $fileName }}" style="{{ $item['style'] }}">
                            @else
                            <img src="{{ env('TRAINING_URL') . '/' . $item['image'] }}"
                                alt="{{ $fileName }}" style="{{ $item['style'] }}">
                            @endif
                            @endif

                            </p>

                            @endforeach
                            @endfor
                    </div>

                    <div class="carousel-button aboutus-partners-carousel-button-right">
                        <span class="fa fa-angle-right partnership-right-btn"></span>

                    </div>
                    <div class="carousel-button aboutus-partners-carousel-button-left">
                        <span class="fa fa-angle-left partnership-left-btn"></span>

                    </div>
                </div>
                @endif

            </div>
            <div class="col-xs-12 col-sm-12  col-md-0 col-lg-0 col-xl-1"></div>
        </div>
    </div>
</section>






<section class="section-sponsorship-policies-block mt-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 sponsorships-image-block p-0">
                <img src="{{ asset('/img/about_us/sponsorships_banner.png') }}" alt="Sponsorships Banner"
                    class="h-100">
            </div>

            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 sponsorships-content-block py-4 mt-0">
                <div class="mx-5 mt-0 sponsorships-full-content">
                    <div class="sponsorship-content mt-2">
                        <div class="aboutus-title sponsorship-block">
                            <h6 class="mb-3">SPONSORSHIPS</h6>
                        </div>
                        <p class="mb-3">Procept gives back to the community through scholarships, awards, charitable
                            donations, and sponsoring events that inspire people to strive for excellence.</p>
                        <a href="{{ route('sponsorships.show') }}">
                            <img src="{{ asset('/img/about_us/read_more_button.png') }}" class="aboutus-readmore-btn mt-2" alt="Read More" width="148" style="height:45px">
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4  policies-content-block p-0">
                <div class="mt-3 mx-4 ms-lg-5 ms-md-5 policy-section">


                    <div class="aboutus-title  policies-title">
                        <h6 class="mb-2">POLICIES</h6>
                    </div>
                    <div class="row mt-4">
                        <div class="col-6 policies-col">
                            <a href="{{ route('accessibility.policy.page') }}">
                                <p>Accessibility ></p>
                            </a>
                            <a href="{{ route('diversity.policy.page') }}">
                                <p>Diversity ></p>
                            </a>
                            <a href="{{ route('drug.and.alcohol.policy.page') }}">
                                <p>Drugs & Alcohol ></p>
                            </a>
                            <a href="{{ route('environmental.policy.page') }}">
                                <p>Environment ></p>
                            </a>
                        </div>
                        <div class="col-6 policies-col">
                            <a href="{{ route('health.and.safety.policy.page') }}">
                                <p>Health & Safety ></p>
                            </a>
                            <a href="{{ route('policy.recording.training.sessions.page') }}">
                                <p>Recording ></p>
                            </a>
                            <a href="{{ route('privacypolicy.show') }}">
                                <p>Privacy ></p>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('layouts.footer')

@endsection
