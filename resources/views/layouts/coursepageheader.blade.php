@include('layouts.page_nav')
<div class="image-banner-container">
    <div class="course-type-image-banner">
        <img src="{{ asset('/img/course_type/' . @$getcoursesTypeImage) }}" alt="{{ @$trainingDetails->title }}"
            class="course-type-banner-image" loading="lazy">
        <div class="overlay course-type-text-container">
            <div class="container training-management-course">
                <div class="row courses-level-list">
                    <div class="col-12 training-content">
                        <h2 class="mb-3 individualcourse-title">{{ @$trainingDetails->title }}</h2>
                        <div class="course-type-header-content">
                            {!! @$trainingDetails->body_value !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
