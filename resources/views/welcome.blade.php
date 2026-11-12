@extends('layouts.home')
@include('layouts.metatag_header')
@section('content')

    @include('layouts.page_nav')


    @include('layouts.header')

    <div class="section-graphical-container">
        <div class="container p-0">
            <div class="row justify-content-center">
                <!-- Training Section -->
                <div class="col-xs-12 col-sm-12  col-md-4 col-lg-4 col-xl-4 mt-5 text-center px-0 ">
                    <div class="graphical-training-icon mb-4">
                        <a href="{{ route('training.page') }}">
                            <img src="{{ asset('/img/services_image/training_icon_cut.png') }}" alt="Training" width="80"
                                loading="lazy">
                        </a>
                    </div>
                    <div class="graphical-training-title content-menu-title mb-2">
                        <a href="{{ route('training.page') }}">
                            <h6>TRAINING</h6>
                        </a>
                    </div>
                    <div class="graphical-training-content text-center">
                        <span>Discover our tailored training</span><br>
                        <span>approach, extensive course catalog,</span><br>
                        <span>and credit-earning opportunities</span><br>
                        <span>to advance your career. Start your</span><br>
                        <span>learning journey with us today!</span>
                    </div>
                    <div class="graphical-training-btn mt-3 mb-5">
                        <a href="{{ route('training.page') }}">
                            <img src="{{ asset('/img/services_image/orange_learn_more_button.png') }}" alt="Training"
                                width="185" loading="lazy">
                        </a>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12  col-md-4 col-lg-4 col-xl-4 mt-5 text-center px-0">
                    <div class="graphical-consulting-icon mb-4">
                        <a href="{{ route('consulting.page') }}">
                            <img src="{{ asset('/img/services_image/consulting_icon_cut.png') }}" alt="Consulting"
                                width="80" loading="lazy">
                        </a>
                    </div>
                    <div class="graphical-consulting-title content-menu-title mb-2">
                        <a href="{{ route('consulting.page') }}">
                            <h6>CONSULTING</h6>
                        </a>
                    </div>
                    <div class="graphical-consulting-content text-center">
                        <span>Unlock success with our consulting</span><br>
                        <span>services. Benefit from expert advisory</span><br>
                        <span>support and explore our detailed</span><br>
                        <span>case studies. Partner with us to</span><br>
                        <span>achieve your business goals!</span>
                    </div>
                    <div class="graphical-consulting-btn mt-3 mb-5">
                        <a href="{{ route('consulting.page') }}">
                            <img src="{{ asset('/img/services_image/blue_learn_more_button.png') }}" alt="Consulting"
                                width="185" loading="lazy">
                        </a>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12  col-md-4 col-lg-4 col-xl-4 mt-5 text-center px-0">
                    <div class="graphical-about-us-icon mb-4">
                        <a href="{{ route('aboutus.page') }}">
                            <img src="{{ asset('/img/services_image/about_us_icon_cut.png') }}" alt="About Us"
                                width="95" loading="lazy">
                        </a>
                    </div>
                    <div class="graphical-about-us-title content-menu-title mb-2">
                        <a href="{{ route('aboutus.page') }}">
                            <h6>ABOUT US</h6>
                        </a>
                    </div>
                    <div class="graphical-about-us-content text-center">
                        <span>Discover who we are, meet</span><br>
                        <span>our dedicated management team</span><br>
                        <span>and expert trainers, and learn</span><br>
                        <span>about our valued partners. Join</span><br>
                        <span>us and thrive together!</span>
                    </div>
                    <div class="graphical-about-us-btn mt-3 mb-5">
                        <a href="{{ route('aboutus.page') }}">
                            <img src="{{ asset('/img/services_image/green_learn_more_button.png') }}" alt="About Us"
                                width="185" loading="lazy">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.upcoming_public_course')


    <section class="events">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-lg-3 p-0">
                    <div class="upcoming-events-img-container w-100 h-100">
                        <img src="{{ asset('/img/upcoming_events/AdobeStock_824701917.png') }}" alt="Upcoming Events"
                            class="w-100 h-100" loading="lazy">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-lg-9 d-flex align-items-center py-3">
                    <div class="event-list w-100 p-3 ps-lg-5 ps-sm-0 ps-md-4 mx-md-3 mx-lg-0 mx-xs-0 mx-sm-0">

                        @if (count($events) > 0)
                            <h6>UPCOMING EVENTS</h6>
                            <table class="w-100">
                                @foreach ($events as $event)
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="upcoming-event-date">
                                                    @php
                                                        $month = date('M', strtotime($event->field_event_date_value));
                                                    @endphp
                                                    <p>{{ $month === 'May'
                                                        ? date('M j', strtotime($event->field_event_date_value))
                                                        : date(
                                                            "M.
                                                                                                j",
                                                            strtotime($event->field_event_date_value),
                                                        ) }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="upcoming-event-free px-4">
                                                    <p class="event-free-btn">
                                                        <img src="{{ asset('/img/upcoming_events/Free icon.png') }}"
                                                            class="event-btn" alt="Upcoming Events" loading="lazy">
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="upcoming-event-name truncate px-4">
                                                    <p>
                                                        @if (strlen($event->title) >= 150)
                                                            <span
                                                                class="short-event-title">{{ substr($event->title, 0, 150) }}...</span>
                                                            <a href="{{ route('getAllSlug', ['slug' => $event->url]) }}"
                                                                class="see-more-event">See more</a>
                                                        @else
                                                            {{ $event->title }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </td>

                                            <td class="upcoming-event-register-col">
                                                <div class="upcoming-event-register px-lg-3">
                                                    <p>
                                                        <a href="{{ route('getAllSlug', ['slug' => $event->url]) }}"
                                                            class="upcoming-event-wrap-register">Register ></a>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                            </table>
                            <div class="events-button">
                                <a href="{{ route('getallevents') }}"  class="event-btn">
                                    <img src="{{ asset('/img/upcoming_events/All Events Button.png') }}"
                                        alt="Upcoming Events" class="" loading="lazy">
                                </a>
                            </div>
                        @else
                            <div class="events-not-found">
                                <h6>UPCOMING EVENTS</h6>
                                <p class="text-left ms-0 mt-4 d-md-block">Events not found</p>
                            </div>

                        @endif
                    </div>
                </div>


            </div>
        </div>
    </section>
    <section class="customer-saying w-100 position-relative text-center testimonial-slider">
        @if ($testimonials->count() > 1)
            <div class="carousel-button carousel-button-left position-absolute slider-control slider-control-left ">
                <span class="fa fa-angle-left"></span>
            </div>
        @endif
        <div class="container p-md-0 p-lg-0 px-5">
            <div class="row">
                <div class="col-12 px-5">
                    <div id="testimonialCarousel" class="customer-saying-carousel w-100 overflow-hidden position-relative py-5 testimonial-wrapper">
                        <h6>WHAT OUR CUSTOMERS ARE SAYING</h6>
                        <div @if ($testimonials->count() > 1) class="customer-saying-container testimonial-track" @endif>
                            <ul class="testimonial-list list-unstyled mb-0 testimonial-items">
                                @foreach ($testimonials as $details)
                                    <li>
                                        <div class="testimonial-content container testimonial-box">
                                            <div class="row align-items-center customer-saying-slider testimonial-slide">
                                                @php
                                                    $has_image =
                                                        isset(
                                                            $details->fieldDataFieldHomepageTestimonialImage
                                                                ->fileManagedHomepageTestimonialImage->filename,
                                                        ) &&
                                                        $details->fieldDataFieldHomepageTestimonialImage
                                                            ->fileManagedHomepageTestimonialImage->filename != '';
                                                @endphp
                                                <div class="customer-saying row testimonial-content">
                                                    <div
                                                        class="{{ $has_image ? 'col-md-10 col-lg-10 col-sm-12 testimonial-short text-start p-0' : 'col-12 testimonial-long text-start p-0' }}">
                                                        @php
                                                            $body_value = isset($details->fieldDataBody->body_value)
                                                                ? $details->fieldDataBody->body_value
                                                                : '';
                                                            $field_data_body_value = strip_tags($body_value);
                                                        @endphp
                                                        <p class="m-0">{!! '"' . htmlspecialchars($field_data_body_value) . '"' !!}</p>
                                                        @if (isset($details->fieldDataFieldSpeaker->field_speaker_value))
                                                            <span class="move-down d-block p-0 testimonial-author">-
                                                                {{ $details->fieldDataFieldSpeaker->field_speaker_value }}</span>
                                                        @endif
                                                    </div>
                                                    @if ($has_image)
                                                        <div
                                                            class="col-md-2 d-flex justify-content-center testimonial-image-short">
                                                            <img class="Testimonial Image testimonial-img"
                                                                src="{{ env('FILE_FOLDER') . '/' . $details->fieldDataFieldHomepageTestimonialImage->fileManagedHomepageTestimonialImage->filename }}"
                                                                width="100" loading="lazy"
                                                                alt="{{ $details->fieldDataFieldHomepageTestimonialImage->fileManagedHomepageTestimonialImage->alt ?? 'Testimonial Image' }}">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @if ($testimonials->count() > 1)
                <div class="carousel-button carousel-button-right position-absolute slider-control slider-control-right">
                    <span class="fa fa-angle-right"></span>
                </div>
            @endif
        </div>
    </section>




    <section class="company-news-link">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-lg-3 col-sm-12 p-0">
                    <div class="company-news-img-container w-100 h-100">
                        <img src="{{ asset('/img/company_news/AdobeStock_423577784.jpg') }}" alt="Company News"
                            class="w-100 h-100" loading="lazy">
                    </div>
                </div>
                <div class="col-md-9 col-lg-9 col-sm-12 d-flex align-items-center py-5">
                    <div>
                        <h6 class="px-3 px-lg-5 px-md-5">
                            COMPANY NEWS
                            <a href="{{ route('rss.feed') }}">
                                <img src="{{ asset('/img/company_news/rss_icon.png') }}" class="rss-news-img mb-1"
                                    alt="RSS" loading="lazy">
                            </a>
                        </h6>
                        <div class="container company-news-conainer-data pt-2 mx-sm-0 px-3 px-lg-5 px-md-5">
                            @foreach ($allCompanyNews as $item)
                                <div class="row company-news-item"> <!-- Added spacing class here -->
                                    <div class="col-12 col-md-3 col-lg-2 company-news-image-content">
                                        <div class="company-news-menu">
                                            @if ($item->filename != '')
                                                <p class="mb-3">
                                                    <a href="{{ route('getAllSlug', ['slug' => $item->url]) }}">
                                                        <img src="{{ env('FILE_FOLDER') . '/' . $item->filename }}"
                                                            class="menu-news-img w-100" alt="Company News"
                                                            loading="lazy">
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-9 col-lg-10 company-news-data-content">
                                        <div class="company-news-list-menu">
                                            <p class="company-news-content my-0">
                                                <a href="{{ route('getAllSlug', ['slug' => $item->url]) }}">
                                                    @if ($item->titleOne)
                                                        <span class="title-part-1">{{ $item->titleTwo }} |</span>
                                                        <span class="title-part-2">{{ $item->titleOne }}</span>
                                                    @else
                                                        <span class="title-part-2">{{ $item->titleTwo }}</span>
                                                    @endif
                                                </a>
                                            </p>
                                            <div class="body-summary">
                                                @php
                                                    $isLast = $loop->last;
                                                    $content =
                                                        $item->fieldDataBody->body_summary ??
                                                        ($item->fieldDataBody->body_value ?? '');
                                                    $truncatedContent = \Illuminate\Support\Str::limit(
                                                        strip_tags($content),
                                                        150,
                                                    );
                                                @endphp

                                                <div class="short-text">
                                                    {{ $truncatedContent }}
                                                    @if ($isLast)
                                                        <span class="d-inline">...</span>
                                                        <a href="{{ route('getAllSlug', ['slug' => $item->url]) }}"
                                                            class="see-more-link">See more</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        {{-- More button --}}
                        <div class="more-news">
                            <a href="{{ route('company.news') }}">
                                <img src="{{ asset('/img/company_news/more_button.png') }}"
                                     class="news-more-img mt-3 py-0" alt="Company News" loading="lazy">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>





    <section id="section-industry-link" class="industry-link mt-5">
        <div class="container industry-section-container px-md-0">
            <div class="row px-3 px-md-0 px-lg-0">
                <div class="col-md-6 d-flex flex-column align-items-center">
                    <div class="industry-list text-center">
                        <h6>INDUSTRY RECOGNITION</h6>
                        <div class="industry-menu-list">
                            <div class="industry-text-content">
                                <span>In addition to numerous industry accreditations (PMI-ATP, IIBA EEP, APMG ATO, CCA Gold
                                    Seal, etc.), Procept and its subsidiaries have earned numerous prestigious awards for
                                    our training and consulting including awards from the Project Management Institute (PMI)
                                    who named us “Continuing Professional Education Provider of the Year”, Institute for
                                    Performance and Learning (I4PL) who gave us their “Gold Award for Training Excellence”,
                                    the Conference Board of Canada, and <i>HR Magazine.</i></span>
                            </div>
                            <div class="industry-awards-accreditations-section mt-3">
                                <a href="{{ route('awards.page') }}" class="industry-awards-info">
                                    <img src="{{ asset('/img/industry_recognition/awards_button.png') }}"
                                        class="news-more-img" alt="Company News" loading="lazy"></a>
                                <a href="{{ route('earningcredits.show') }}" class="industry-accreditations-info"> <img
                                        src="{{ asset('/img/industry_recognition/accreditations_button.png') }}"
                                        class="industry-accr-img" alt="Company News" loading="lazy"></a>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-0 d-flex justify-content-center">
                    <div class="industry-recognition-slider">
                        <div class="logos-slider">
                            @php
                                $loopCount = 10;
                            @endphp

                            @for ($i = 0; $i < $loopCount; $i++)
                                @foreach ($industryRecLogoslider as $item)
                                    @php
                                        $industryImage = $item->uri;
                                        if ($industryImage) {
                                            $pathParts = explode(config('app_constants.PUBLIC_FOLDER'), $industryImage);
                                            $relativePath = isset($pathParts[1]) ? $pathParts[1] : $industryImage;
                                            $imageUrl =
                                                env('INDUSTRY_RECO_FILE_FOLDER') . $relativePath;
                                        }
                                    @endphp

                                    <div class="slide">

                                        @if (!empty($item->image_url))
                                            <a href="{{ $item->image_url }}" target="_blank">
                                                <img src="{{ $imageUrl }}" class="img-fluid"
                                                    alt="{{ $industryImage }}" loading="lazy">
                                            </a>
                                        @else
                                            <img src="{{ $imageUrl }}" class="img-fluid" alt="{{ $industryImage }}"
                                                loading="lazy">
                                        @endif

                                    </div>
                                @endforeach
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="section-bottom-menu" class="bottom-menu mt-5 py-5">
        <div class="container bottom-menu-container">
            <div class="row">
                <div class="col-12 col-md-4 mb-4 text-center text-md-start px-0 mb-md-0">
                    <div>
                        <div class="bottom-menu-logo">
                            <a href="/"><img src="{{ asset('/img/information/procept_logo.png') }}" alt="Procpet"
                                    class="bottom-menu-img-logo" loading="lazy"></a>
                            <span class="bottom-logo-text d-block mt-2 mx-5 text-nowrap ms-1">Improving your project delivery
                                excellence.</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-center mb-4 px-3 mb-md-0">
                    <div class="bottom-menu-location text-center">
                        <h6>HEAD OFFICE</h6>
                        <p class="mb-0 mt-2">Procept Associates Ltd.<br> 18 King St. East, Suite 1400 <br>Toronto, ON
                            M5C IC4</p>
                        <p><a href="tel:1-800-261-6861">1-800-261-6861</a> |
                            <a href="mailto:info@procept.com" class="footer-mail-link">info@procept.com</a>
                        </p>
                        <a href="{{ route('contact.show') }}" class="d-inline-block mt-3 ">
                            <img src="{{ asset('/img/information/other_location_button.png') }}" class="location-btn"
                                alt="Contact" loading="lazy">
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-4  px-3 mb-md-0">
                    <div class="bottom-menu-working-with-us">
                        <h6>WORKING WITH US</h6>
                        <ul class="working-with-us-menu list-unstyled mb-0">
                            <li><a href="{{ route('working.for.us') }}">Careers</a></li>
                            <li><a href="{{ route('paying-us') }}">Paying Bills</a></li>
                            <li><a href="{{ route('requesting.quote') }}">Request a Price Quote</a></li>
                            <li><a href="{{ env('CUSTOMER_PORTAL') }}" target="_blank">Members Portal</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')
@endsection
