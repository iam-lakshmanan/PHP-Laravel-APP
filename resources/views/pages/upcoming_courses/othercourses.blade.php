@extends('layouts.training_page_home')
@include('layouts.metatag_header')

@section('content')
    @include('layouts.coursepageheader')


    <section class="courses-level-container">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-12 mt-5">
                    @foreach ($courseLevel as $levelData)
                        @php
                            $levelCourses = $getOtherCourses->where('field_course_level_tid', $levelData->tid);
                        @endphp

                        @if ($levelCourses->isNotEmpty())
                            <div class="mt-5">
                                <img src="{{ asset('img/course_listing/' . @$levelData->icon) }}"
                                    alt="{{ @$levelData->name }}-icon" width="95" loading="lazy">

                                <h5 class="{{ @$levelData->titleClass }} mt-3">
                                    {{ strtoupper(@$levelData->name) }} COURSES
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
                                            @foreach ($levelCourses as $course)
                                                <div class="col-2 course-id">
                                                    <p>{{ @$course->field_course_id_value }}</p>
                                                </div>
                                                <div class="col-7 course-title">
                                                    <p><a
                                                            href="{{ route('getAllSlug', ['slug' => @$course->course_url]) }}">
                                                            {{ @$course->title }}
                                                        </a></p>
                                                </div>
                                                <div class="col-3 course-duration">
                                                    <p>{{ @$course->field_duration_value }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('layouts.advertisement')
    @include('layouts.footer')
@endsection
