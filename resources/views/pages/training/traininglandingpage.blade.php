@extends('layouts.training_page_home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.page_nav')

<div class="training-landing-banner">
    <img src="{{ asset('/img/training_landing_bg/training_landing_bg.png') }}" alt="training-banner img"
        class="training-banner" loading="lazy">
    <div class="overlay training-landing-container container-fluid">
        <div class="container training-landing-course d-flex">
            <div class="row">
                <div class="training-landing-title col-12 text-center">
                    <h3>Training</h3>
                </div>
                <div class="col-12 training-landing-content">
                    <p class="mt-2">
                        At Procept, we offer a comprehensive range of professional courses designed to enhance your
                        skills
                        and advance your career. Our programs include Project Management, Change Management, Business
                        and Data
                        Analytics, Leadership, and General Management courses, and much more! Whether you're looking to
                        deepen
                        the expertise of yourself or your employees, or explore new areas, our courses provide the tools
                        and
                        knowledge to achieve your objectives.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>


<section id="courses-category">
    <div class="container">
        <div class="row justify-content-between">
            <!-- Course Category Item -->
            <div class="col-12 col-md-6 col-lg-2 mb-2 d-flex flex-column align-items-center text-center">
                <a href="{{ route('training.pmcourses.page') }}">
                    <img src="{{ asset('/img/training_areas/project_management_icon.png') }}"
                        alt="Project Management Icon" class="training-courses-banner pm-banner mt-5" loading="lazy">
                </a>
                <a href="{{ route('training.pmcourses.page') }}">
                    <h4 class="training-category-title mt-4  text-wrap" style="width: 12rem">PROJECT MANAGEMENT</h4>
                </a>
                <p class="course-category-title pm-content text-wrap" style="width: 11rem">Acquire the skills to
                    effectively plan,
                    execute,
                    and complete projects
                    to maximize benefits.</p>
                <a href="{{ route('training.pmcourses.page') }}" class="d-block">
                    <img src="{{ asset('/img/training_areas/project_management_btn.png') }}"
                        alt="View Project Management Courses" class="training-courses-button mx-auto mb-4 d-block"
                        loading="lazy">
                </a>
            </div>

            <!-- Repeat for other courses -->
            <div class="col-12 col-md-6 col-lg-2 mb-4 d-flex flex-column align-items-center text-center">
                <a href="{{ route('training.cmcourses.page') }}">
                    <img src="{{ asset('/img/training_areas/change_management_icon.png') }}"
                        alt="Change Management Icon" class="training-courses-banner mt-5" loading="lazy">
                </a>
                <a href="{{ route('training.cmcourses.page') }}">
                    <h4 class="training-category-title mt-4 text-wrap" style="width: 12rem">CHANGE MANAGEMENT</h4>
                </a>
                <p class="course-category-title text-wrap" style="width: 12rem">Learn how to successfully manage and
                    implement
                    organizational
                    changes,
                    ensuring smooth transitions.
                </p>
                <a href="{{ route('training.cmcourses.page') }}" class="d-block">
                    <img src="{{ asset('/img/training_areas/change_management_btn.png') }}"
                        alt="View Change Management Courses" class="training-courses-button mx-auto mb-4"
                        loading="lazy">
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-2 mb-4 d-flex flex-column align-items-center text-center">
                <a href="{{ route('training.bacourses.page') }}">
                    <img src="{{ asset('/img/training_areas/business_analytics_icon.png') }}"
                        alt="Business Analytics Icon" class="training-courses-banner mt-5" loading="lazy">
                </a>
                <a href="{{ route('training.bacourses.page') }}">
                    <h4 class="training-category-title mt-4  text-wrap" style="width: 14rem">BUSINESS AND<br> DATA
                        ANALYTICS</h4>
                </a>
                <p class="course-category-title text-wrap" style="width: 12rem">Master business processes and gain
                    valuable
                    insights from
                    data, driving
                    informed business decisions.</p>
                <a href="{{ route('training.bacourses.page') }}" class="d-block">
                    <img src="{{ asset('/img/training_areas/business_analytics_btn.png') }}"
                        alt="View Business Analytics Courses" class="training-courses-button mx-auto mb-4"
                        loading="lazy">
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-2 mb-4 d-flex flex-column align-items-center text-center">
                <a href="{{ route('training.leadershipcourses.page') }}">
                    <img src="{{ asset('/img/training_areas/leadership_management_icon.png') }}"
                        alt="Leadership Management Icon" class="training-courses-banner mt-5" loading="lazy">
                </a>
                <a href="{{ route('training.leadershipcourses.page') }}">
                    <h4 class="training-category-title mt-4 text-wrap" style="width: 14rem">LEADERSHIP AND<br>
                        MANAGEMENT</h4>
                </a>
                <p class="course-category-title text-wrap" style="width: 12rem ">Cultivate the qualities and skills
                    necessary to
                    craft strategies and
                    inspire others to achieve success.</p>
                <a href="{{ route('training.leadershipcourses.page') }}" class="d-block">
                    <img src="{{ asset('/img/training_areas/leadership_management_btn.png') }}"
                        alt="View Leadership and Management Courses" class="training-courses-button mx-auto mb-4"
                        loading="lazy">
                </a>
            </div>

                <div class="col-12 col-md-6 col-lg-2 mb-4 d-flex flex-column align-items-center text-center">
                    <a href="{{ route('training.othercourses.page') }}"> <img
                            src="{{ asset('/img/training_areas/other_courses_icon.png') }}" alt="Other Courses Icon"
                            class="training-courses-banner other-courses-banner mt-5" loading="lazy"></a>
                    <a href="{{ route('training.othercourses.page') }}">
                        <h4 class="training-category-title mt-4 text-wrap" style="width: 12rem">OTHER <br> COURSES</h4>
                    </a>
                    <p class="course-category-title text-wrap" style="width: 11rem">Explore our diverse selection of other
                        courses
                        designed to
                        help
                        you expand your expertise!</p>
                    <a href="{{ route('training.othercourses.page') }}" class="d-block"> <img
                            src="{{ asset('/img/training_areas/other_courses_btn.png') }}" alt="View Other Courses"
                            class="training-courses-button mx-auto mb-4" loading="lazy"></a>
                </div>
            </div>
        </div>
    </section>
    @include('layouts.upcoming_public_course')


<section id="training-landing-credits" class="">
    <div class="container">
        <div class="row justify-content-center">
            <div
                class="col-xs-12 col-sm-12  col-md-6 col-lg-4 col-xl-4 mt-5 mb-5 text-center d-flex flex-column align-items-center">
                <h4 class="training-credits-title">TRAINING EXPERIENCE</h4>
                <div class="training-credits-content">
                    <span>Since 1963, our practical,</span><br>
                    <span>hands-on training prepares you for</span><br>
                    <span>for real-world challenges.</span><br>
                </div>
                    <a href="{{config('app_constants.TRAINING_EXPERIENCE') }}" class="training-credits-link">Learn more
                        ></a>
                </div>
                <div
                    class="col-xs-12 col-sm-12  col-md-6 col-lg-4 col-xl-4 mt-5 mb-5 text-center d-flex flex-column align-items-center">
                    <h4 class="earn-credits-title">EARN CREDITS</h4>
                    <div class="training-credits-content">
                        <span>Our courses help you earn valuable</span><br>
                        <span>professional certification and continuing</span><br>
                        <span>education credits to advance your career.</span><br>
                    </div>
                    <a href="{{ route('earningcredits.show') }}" class="earn-credits-link">Learn more ></a>

            </div>
            <div
                class="col-xs-12 col-sm-12  col-md-6 col-lg-4 col-xl-4 mt-5 mb-5 text-center d-flex flex-column align-items-center">
                <h4 class="exampass-credits-title">EXAM PASS GUARANTEE</h4>
                <div class="training-credits-content">
                    <span>We stand by our training with an</span><br>
                    <span>exam pass guarantee, ensuring you</span><br>
                    <span>achieve your certification goals.</span>
                </div>

                <a href="{{ route('exampassguarantees.show') }}" class="exampass-credits-link">Learn
                    more ></a>
            </div>
        </div>
    </div>
</section>



@include('layouts.advertisement')
@include('layouts.footer')
@endsection
