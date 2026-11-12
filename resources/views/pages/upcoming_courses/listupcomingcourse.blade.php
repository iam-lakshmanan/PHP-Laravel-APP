@extends('layouts.team_page_home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.individualcourseheader')


<section id="section-upcoming-courses-page">
    <div class="container-fluid">
        <div class="row d-flex justify-content-end gap-5 mt-2">
            <div class="upcoming-courses-list col-12 col-md-7">
                <div class="upcoming-courses-content">
                    <div class="abstract-drop-content">{!! @$courseData->field_abstract_value !!}</div>
                </div>


            </div>

            <div class="upcoming-courses-list-fullleft col-12 col-md-4 mt-0">

    @if (count($testimonialsData) > 0)
    <div class="upcoming-courses-content-left">
    <div id="testimonial_carousel" class="testimonial-carousel">
        <div class="container testimonial-container">
            <h6 class="upcoming-courses-testimonials-title list" style="margin-top:21px;">TESTIMONIALS</h6>
            <ul class="upcoming-testimonial-list">
                @foreach ($testimonialsData as $testimonial)
                    <li>
                        <div class="upcoming-testimonial-content testimonials-courses-data">
                            <div class="row">
                                <div class="col-12 p-0">
                                    <span class="testimonial-content list-upcoming" data-testimonial-id="{{ $testimonial->id }}">
                                        @php
                                            $bodyText = strip_tags($testimonial->FieldDataBodyCourse->body_value);
                                            $shortText = substr($bodyText, 0, 160);
                                            $isLongText = strlen($bodyText) > 160;
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
                            <div class="row">
                                <div class="col-3 col-lg-3 col-md-12 p-md-0 testimonials-images">
                                    @php
                                        $testimonialImage = @$testimonial->FieldDataFieldTestimonialImage->FileManagedTestimonialImage->uri;
                                        if ($testimonialImage) {
                                            $pathParts = explode(config('app_constants.PUBLIC_FOLDER'), $testimonialImage);
                                            $relativePath = isset($pathParts[1]) ? $pathParts[1] : $testimonialImage;
                                            $imageUrl = env('FILE_FOLDER') . '/' . $relativePath;
                                        } else {
                                            $imageUrl = asset('/img/upcoming_public_courses/user.jpg');
                                        }
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="Testimonial Image" class="testimonial-img img-fluid" loading="lazy">
                                </div>
                                <div class="col-9 col-lg-9 col-md-12 testimonials-text-content">
                                    <p class="testimoinals-title-text">{{ @$testimonial->field_author_value}}</p>
                                    <p class="position-text">
                                        {{ @$testimonial->FieldDataFieldTestimonialPositionNode->field_testimonial_position_value }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($testimonialcount > 10)
                @php
                    $encryptedNodeId = Crypt::encryptString($testimonial->field_course_target_target_id);
                @endphp
                <a class="view-all-content" href="{{ route('course.testimonials', ['nodeId' => $encryptedNodeId]) }}">View All</a>
            @endif

            <div class="dots-container test-testimonials"></div>
        </div>
    </div>
</div>

                @endif

                <div class="upcoming-courses-content-info mx-md-0 mx-lg-5 mx-5">
                    <div class="upcoming-testimonial-courses-info mt-4">

                        <h6 class="courses-info-title">COURSE INFO</h6>
                        <div class="course-info-content">
                            @if (isset($courseData->fieldDataFieldCourseId->field_course_id_value))
                            <p> Course ID: {{ @$courseData->fieldDataFieldCourseId->field_course_id_value }}</p>
                            @endif
                            @if (isset($courseData->name))
                            <p> Course Level: {{ @$courseData->name }}</p>
                            @endif
                            @if (isset($courseData->fieldDataFieldDuration->field_duration_value))

                            <p> Duration: {{ @$courseData->fieldDataFieldDuration->field_duration_value }}</p>
                            @endif
                        </div>


                        @if(@$courseData->field_brochure_link_value)
                        <div class="brochure-details">
                            <a href="{{ @$courseData->field_brochure_link_value }}" target="_blank"><img
                                    src="{{asset('/img/upcoming_public_courses/brochure_button.png')}}" loading="lazy"
                                    class="brochue-more-img" alt="learn-icon"></a>
                        </div>
                        @endif

                        {{-- @if(isset($videolinkiframe) && !empty($videolinkiframe))
                        <div id="video-container">
                            <h3 class="video-title mt-5">VIDEO TESTIMONIALS</h3>

                            @foreach($videolinkiframe as $src)
                            <div class="video-wrapper">
                                <iframe class="video-frame" width="250" height="200" src="{{ $src }}" frameborder="0"
                                    allow="autoplay clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen style="width:90%; height:200px;"></iframe>

                            </div>
                            @endforeach
                        </div>
                        <div id="dots-container">
                        </div>
                        @endif --}}

                        @if(isset($videolinkiframe) && !empty($videolinkiframe))
                        <div class="video-slider-container-new" id="video-container">
                            <h3 class="video-title mt-5">VIDEO TESTIMONIALS</h3>
                            <div class="video-slider-new" id="slider">
                                @foreach($videolinkiframe as $index => $src)
                              <div class="video-slide-new" class="video-wrapper">

                                <iframe id="video{{ $index }}" class="video-frame-new" width="250" height="200"  src="{{ strpos($src, '?') === false ? $src . '?enablejsapi=1' : $src . '&enablejsapi=1' }}"  frameborder="0" style="width:90%; height:200px;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                              </div>
                              @endforeach
                            </div>

                            <!-- Dots for navigation -->
                            <div class="dots-container-new">
                              @foreach($videolinkiframe as $index => $src)
                              <span class="dot-new" data-video="{{ $index }}"></span>
                          @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="upcoming-courses-sessions">
                            <h6 class="upcoming-courses-sessions-title">UPCOMING SESSIONS</h6>

                            @if (!empty($upcomingSessionData) && count($upcomingSessionData) > 0)

                            @foreach ($upcomingSessionData as $sessionData)

                            <div class="row">
                                <div class="col-md-6 upcoming-session-img">
                                    @if ($sessionData->field_online_value == config('app_constants.LOCATION_ONLINE'))
                                    <p>
                                        <img src="{{ asset('/img/upcoming_public_courses/online_course_icon.png') }}" loading="lazy"
                                            alt="online_course_icon">
                                    </p>
                                    @elseif (
                                    $sessionData->field_online_value ==
                                    config('app_constants.LOCATION_INPERSON')
                                    )
                                    <p>
                                        <img src="{{ asset('/img/upcoming_public_courses/in_person_course_icon.png') }}" loading="lazy"
                                            alt="in_person_course_icon">
                                    </p>
                                    @endif
                                </div>

                                <div class="col-md-6 upcoming-sessions">
                                    @php
                                    $sessionDate = $sessionData->field_session_dates_value
                                    ? \Carbon\Carbon::parse($sessionData->field_session_dates_value)
                                    : \Carbon\Carbon::parse($sessionData->field_start_date1_value);

                                    $formattedSessionDate = $sessionDate->format('F j, Y');
                                    $daysUntilSession = $sessionDate->diffInDays(\Carbon\Carbon::now());
                                    @endphp

                                    @php
                                    $groupedTitles = [];

                                    for ($i = 1; $i <= 10; $i++) { $instructorField="fieldDataFieldInstructor$i" ;
                                        $instructorNodeField="fieldDataFieldInstructorNode$i" ; if ( isset(
                                        $sessionData->
                                        $instructorField->$instructorNodeField->title
                                        )
                                        ) {
                                        $instructorTitle = $sessionData->$instructorField->$instructorNodeField->title;

                                        if (!in_array($instructorTitle, $groupedTitles)) {
                                        $groupedTitles[] = $instructorTitle;
                                        }
                                        }
                                        }

                                                    $instructorNames = [];
                                                    $courseInstructorTitle = optional(optional($sessionData->fieldDataFieldCourseInstructor)->fieldCourseInstructorNode)->title;

                                                    // ignore placeholder values
                                                    if (!empty($courseInstructorTitle) && strtoupper(trim($courseInstructorTitle)) !== 'TBD') {
                                                        $instructorNames[] = trim($courseInstructorTitle);
                                                    }

                                                    foreach ($groupedTitles as $title) {
                                                        $title = trim($title);
                                                        if (empty($title) || strtoupper($title) === 'TBD') {
                                                            continue;
                                                        }

                                                        if (!in_array($title, $instructorNames)) {
                                                            $instructorNames[] = $title;
                                                        }
                                                    }
                                                @endphp

                                                Begins {{ $formattedSessionDate }}<br>
                                                Instructor(s):
                                                @if (count($instructorNames) > 0)
                                                    {{ implode(', ', $instructorNames) }}
                                                @else
                                                    Not Available
                                                @endif <br>
                                            <b>Starts in {{ $daysUntilSession }} days</b><br>
                                            <a href="{{ @$sessionData->field_if_yes_eventbrite_link_value }}"
                                                target="_blank" class="course-register">Register ></a>
                                            </p>
                                </div>
                            </div>
                            @endforeach

                                    @php
                                        $currentUrl = url()->current();
                                        $decodedUrl = urldecode($currentUrl);
                                        $baseUrl = url()->to('/');
                                        $baseUrlWithSlash = rtrim($baseUrl, '/') . '/';
                                        $relativeUrl = str()->after($decodedUrl, $baseUrlWithSlash);

                                    @endphp

                            <div class="session-button-details">
                                <a href="{{ route('upcomingcourses.sessions.list', ['course_title_slug' => $relativeUrl]) }}"
                                    target="_blank">
                                    <img src="{{ asset('/img/upcoming_public_courses/view_all_button.png') }}"
                                        class="session-all-img" alt="learn-icon" loading="lazy">
                                </a>
                            </div>

                            @else
                            <p>Sessions not found.</p>
                            @endif
                        </div>


                        @if (@$courseData->field_pdu_value)
                        <div class="pmi-pdu-breakdown mt-4">
                            <h6 class="pmi-pdu-breakdown-title">PMI PDU BREAKDOWN</h6>
                            <div class="pmi-pdu-breakdown-content">
                                @php
                                $content = $courseData->field_pdu_value ?? '';
                                $baseUrl = env('TRAINING_URL');


                                $updatedContent = preg_replace_callback(
                                '/<img\s+[^>]*src="([^"]*)"/i',
                                    function ($matches) use ($baseUrl) {
                                    $src = $matches[1];
                                    if (!preg_match('/^https?:\/\//i', $src)) {
                                    $src = $baseUrl . ltrim($src, '/');
                                    }
                                    return str_replace($matches[1], $src, $matches[0]);
                                    },
                                   '<div>' . $content . '</div>'
                                    );


                                    $updatedContent = str_replace(
                                    config('app_constants.VIEW_ALL_CREDIT'),
                                    '<img src="' . asset('/img/upcoming_public_courses/all_credits_button.png') . '"
                                        class="all-credits-img" alt="learn-icon">',
                                    $updatedContent
                                    );

                                    $updatedContent = str_replace(
                                    '<p>The three columns in the above',
                                        '<br>
                                    <p>The three columns in the above',
                                        $updatedContent
                                        );

                                        @endphp

                                    <p>{!! $updatedContent !!}</p>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>

</section>

@include('layouts.footer')
@endsection
