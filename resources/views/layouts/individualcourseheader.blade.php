@include('layouts.page_nav')
<div class="individualcourse-image-banner-container">
    <div class="individualcourse-image-banner">
        <img src="{{ asset('/img/course_type/' . @$getcoursesTypeImage) }}" alt="{{ @$courseData->title }}"
            class="individualcourse-banner-image" loading="lazy">
        <div class="overlay individualcourse-text-container">
            <div class="container individualcourse-management">
                <h2 class="mb-3 individualcourse-title">{{ @$courseData->title }}</h2>

            </div>
        </div>
    </div>
</div>
