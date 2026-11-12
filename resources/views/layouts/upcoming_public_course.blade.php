<section class="upcoming-course-link">
    <div class="container-fluid">
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-lg-8 px-lg-5 px-md-5 px-4 mx-md-0 mx-lg-0 upcoming-course-link-padding">
                <div class="upcoming-course-title mx-lg-5 mx-md-5 my-4 py-2">
                    <h6 class="upcoming-public-courses-title mx-0 pt-3">UPCOMING PUBLIC COURSES</h6>
                </div>
                <div class="upcoming-course-link-table ms-lg-5 ms-md-5">
                    <table class="table-responsive upcoming-course-link-fulltable">
                        <thead>
                            <tr>
                                <th style="width: 147px;" class="px-0 py-3">START DATE</th>
                                <th class="px-0 py-3">COURSE NAME</th>
                                <th style="width: 115px;" class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($upcomingCourses as $courses)
                                <tr>
                                    <td>
                                        @php
                                            $sessionDate = strtotime(
                                                $courses->field_choose_session_type_value == 'Contiguous'
                                                    ? $courses->field_session_dates_value
                                                    : $courses->field_start_date1_value,
                                            );
                                            $month = date('M', $sessionDate);
                                        @endphp

                                                                @if ($month == 'May')
                                                                                                @if (
                                                                                                        $courses->title != 'vacation' ||
                                                                                                        $courses->fieldDataFieldCourseNodeDetails->fieldDataFieldCourseNode->title !=
                                                                                                        'vacation'
                                                                                                    )
                                                                                                                                <p>{{ date('M d', $sessionDate) }}</p>
                                                                                                @endif
                                                                @else
                                                                                                @if (
                                                                                                        $courses->title != 'vacation' ||
                                                                                                        $courses->fieldDataFieldCourseNodeDetails->fieldDataFieldCourseNode->title !=
                                                                                                        'vacation'
                                                                                                    )
                                                                                                                                <p>{{ date('M. d', $sessionDate) }}</p>
                                                                                                @endif
                                                                @endif
                                                            </td>
                                                            <td class="upcoming-courses-course-full-content">

                                                                @if (
                                                                        $courses->title != 'vacation' &&
                                                                        $courses->fieldDataFieldCourseNodeDetails->fieldDataFieldCourseNode->title !=
                                                                        'vacation'
                                                                    )
                                                                                                    <p>{!! $courses->fieldDataFieldCourseNodeDetails->fieldDataFieldCourseNode->title
                                                                                                        !!}</p>
                                                                @endif
                                                            </td>
                                                            <td class="upcoming-register-link">
                                                                @if (
                                                                        $courses->title != 'vacation' ||
                                                                        $courses->fieldDataFieldCourseNodeDetails->fieldDataFieldCourseNode->title !=
                                                                        'vacation'
                                                                    )
                                                                                                    <a href="{{ @$courses->fieldDataFieldResaleNode->fieldDataFieldProceptSellTicketCourse->fieldDataFieldIfYesEventbriteLinkResale->field_if_yes_eventbrite_link_value }}"
                                                                                                        target="_blank" class="link-light upcoming-courses-register mb-3">Register ></a>
                                                                @endif
                                                            </td>

                                                        </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>
                        <a href="{{ route('upcoming.public.course.list') }}">
                            <img src="{{ asset('/img/upcoming_public_courses/upcoming_courses_button.png') }}"
                                alt="Upcoming Public Courses" class="mb-4 pb-3" width="261" loading="lazy">
                        </a>
                    </div>

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-lg-4 p-0 upcoming-courses-image-section">
                <div class="w-100 h-100">
                    <img src="{{ asset('/img/upcoming_public_courses/upcoming_public_courses.jpeg') }}"
                        alt="View All Upcoming Public Courses" class="upcoming-courses-link-img w-100 h-100"
                        loading="lazy">
                </div>

            </div>

        </div>
    </div>
</section>
