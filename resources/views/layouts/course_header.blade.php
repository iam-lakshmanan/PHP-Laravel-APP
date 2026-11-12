<div class="individualcourse-image-banner-container">
    <div class="individualcourse-image-banner">
        <img src="{{ asset('/img/upcoming_public_courses/upcoming_courses_banner.png') }}" alt="Upcoming Public Courses"
            class="individualcourse-banner-image" loading="lazy">
        <div class="overlay individualcourse-text-container">
            <div class="container individualcourse-management">
                <h2 class="mb-3 individualcourse-title mt-2"> @php
                    $currentUrl = url()->current();
                    $relativeUrl = str_replace(url('/'), '', $currentUrl);
                @endphp
                    @if ($relativeUrl === route('upcoming.public.course.list', [], false))
                        UPCOMING PUBLIC COURSES
                    @else
                        {{ @$courseTitleData->title }}
                    @endif
                </h2>

            </div>
        </div>
    </div>
</div>
