@extends('layouts.training_page_home')
@include('layouts.metatag_header')

@section('content')

@include('layouts.coursepageheader')


<section class="courses-level-container">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-12 mt-5">
                @if ($getCMCourses->firstWhere('name', config('app_constants.BEGINNER')))
                                @php
                                    $beginnerCourse = $getCMCourses->firstWhere('name', config('app_constants.BEGINNER'));
                                    $backgroundImage = config('courses.BACKGROUND_IMAGES')[$beginnerCourse->title] ??
                                        'default_background.jpeg';
                                @endphp
                                <div style="background-image: url('{{ asset('/img/course_listing/' . $backgroundImage) }}');">

                                    <div>
                                        <img src="{{ asset('/img/course_listing/beginner_icon.png') }}" alt="beginner-icon" width="95" loading="lazy">
                                        <h5 class="beginner-course-title mt-3">{{ strtoupper($beginnerCourse->name) }}
                                            COURSES
                                        </h5>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="row training-courses-title container-fluid mb-3 mx-sm-0 mx-xs-0 mx-md-5 mx-lg-5">
                                                    <div class="course-header-item col-2">COURSE ID</div>
                                                    <div class="course-header-item col-7">COURSE NAME</div>
                                                    <div class="course-header-item col-3">DURATION</div>
                                                </div>
                                                <div class="row course-body container-fluid mt-2 mx-sm-0 mx-xs-0 mx-md-5 mx-lg-5">
                                                    @foreach ($getCMCourses as $course)
                                                        @if ($course->name == config('app_constants.BEGINNER'))

                                                            <div class="col-2 course-id">
                                                                <p>{{ $course->field_course_id_value }}</p>
                                                            </div>
                                                            <div class="col-7 course-title">
                                                                <p><a href="{{ route('getAllSlug', ['slug' => $course->course_url]) }}"
                                                                        >
                                                                        {{ $course->title }}
                                                                    </a></p>
                                                            </div>
                                                            <div class="col-3 course-duration">
                                                                <p>{{ $course->field_duration_value }}</p>
                                                            </div>

                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                @endif


                @if ($getCMCourses->firstWhere('name', config('app_constants.INTERMEDIATE')))
                                @php
                                    $intermediateCourse = $getCMCourses->firstWhere('name', config('app_constants.INTERMEDIATE'));
                                    $backgroundImage = config('courses.BACKGROUND_IMAGES')[$intermediateCourse->title] ??
                                        'default_background.jpeg';
                                @endphp

                                <div class="mt-5"
                                    style="background-image: url('{{ asset('/img/course_listing/' . $backgroundImage) }}');">

                                    <div>
                                        <img src="{{ asset('/img/course_listing/intermediate_icon.png') }}" alt="intermediate-icon"
                                            width="95" loading="lazy">
                                        <h5 class="intermediate-course-title mt-3">
                                            {{ strtoupper($intermediateCourse->name) }}
                                            COURSES
                                        </h5>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="row training-courses-title container-fluid mb-3 mx-sm-0 mx-xs-0 mx-md-5 mx-lg-5">
                                                    <div class="course-header-item col-2">COURSE ID</div>
                                                    <div class="course-header-item col-7">COURSE NAME</div>
                                                    <div class="course-header-item col-3">DURATION</div>
                                                </div>
                                                <div class="row course-body container-fluid mt-2 mx-sm-0 mx-xs-0 mx-md-5 mx-lg-5">
                                                    @foreach ($getCMCourses as $course)
                                                        @if ($course->name == config('app_constants.INTERMEDIATE'))

                                                            <div class="col-2 course-id">
                                                                <p>{{ $course->field_course_id_value }}</p>
                                                            </div>
                                                            <div class="col-7 course-title">
                                                                <p><a href="{{ route('getAllSlug', ['slug' => $course->course_url]) }}"
                                                                        >
                                                                        {{ $course->title }}
                                                                    </a></p>
                                                            </div>
                                                            <div class="col-3 course-duration">
                                                                <p>{{ $course->field_duration_value }}</p>
                                                            </div>

                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                @endif

                @if ($getCMCourses->firstWhere('name', config('app_constants.ADVANCED')))
                                @php
                                    $advancedCourse = $getCMCourses->firstWhere('name', config('app_constants.ADVANCED'));
                                    $backgroundImage = config('courses.BACKGROUND_IMAGES')[$advancedCourse->title] ??
                                        'default_background.jpeg';
                                @endphp

                                <div class="mt-5"
                                    style="background-image: url('{{ asset('/img/course_listing/' . $backgroundImage) }}');">

                                    <div>
                                        <img src="{{ asset('/img/course_listing/advanced_icon.webp') }}" alt="advanced-icon" width="95" loading="lazy">
                                        <h5 class="advanced-course-title mt-3">
                                            {{ strtoupper($advancedCourse->name) }}
                                            COURSES
                                        </h5>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="row training-courses-title container-fluid mb-3 mx-sm-0 mx-xs-0 mx-md-5 mx-lg-5">
                                                    <div class="course-header-item col-2">COURSE ID</div>
                                                    <div class="course-header-item col-7">COURSE NAME</div>
                                                    <div class="course-header-item col-3">DURATION</div>
                                                </div>
                                                <div class="row course-body container-fluid mt-2 mx-sm-0 mx-xs-0 mx-md-5 mx-lg-5">
                                                    @foreach ($getCMCourses as $course)
                                                        @if ($course->name == config('app_constants.ADVANCED'))

                                                            <div class="col-2 course-id">
                                                                <p>{{ $course->field_course_id_value }}</p>
                                                            </div>
                                                            <div class="col-7 course-title">
                                                                <p><a href="{{ route('getAllSlug', ['slug' => $course->course_url]) }}"
                                                                        >
                                                                        {{ $course->title }}
                                                                    </a></p>
                                                            </div>
                                                            <div class="col-3 course-duration">
                                                                <p>{{ $course->field_duration_value }}</p>
                                                            </div>

                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                @endif
            </div>
        </div>
    </div>
</section>



@include('layouts.advertisement')
@include('layouts.footer')

@endsection
