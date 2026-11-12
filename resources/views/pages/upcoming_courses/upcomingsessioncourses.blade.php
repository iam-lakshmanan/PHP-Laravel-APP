@extends('layouts.home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.page_nav')
@include('layouts.course_header')

<section id="section-upcoming-courses-list">
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="table-container upcoming-courses-table mt-5 mb-5 lh-1">
                    <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>LOCATION</th>
                                <th>START DATE</th>
                                <th>INSTRUCTOR(S)</th>
                                <th>REGISTER</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courseSessionsData as $courseDetails)


                                                        <tr>
                                                            <td>
                                                                @php
                                                                    $field_online_value = '';
                                                                    $course_icon = '';
                                                                    $location_title = '';

                                                                    $online_value =
                                                                        @$courseDetails->FieldDataFieldSessionLocLocation->fieldDataFieldOnline->field_online_value;

                                                                    if ($online_value == config('app_constants.LOCATION_ONLINE')) {
                                                                        $field_online_value = 'Online';
                                                                        $course_icon = '/img/upcoming_public_courses/online_course_icon.png';
                                                                    } elseif ($online_value == config('app_constants.LOCATION_INPERSON')) {
                                                                        $field_online_value = 'In Person';
                                                                        $course_icon = '/img/upcoming_public_courses/in_person_course_icon.png';
                                                                        $location_title = @$courseDetails->FieldDataFieldSessionLocLocation->fieldDataFieldSessionLocation->FieldDataFieldSessionLocationNode->title;
                                                                    }
                                                                @endphp

                                                                <div class="upcoming-public-courses-location mt-3">
                                                                    @if ($course_icon)
                                                                        <p class="course-image">
                                                                            <img src="{{ asset($course_icon) }}"
                                                                                alt="{{ $field_online_value }}_course_icon"
                                                                                class="upcoming-public-courses-image" loading="lazy">
                                                                        </p>
                                                                    @endif

                                                                    <span class="upcoming-public-courses-type">{{ $field_online_value }}</span>

                                                                    @if ($location_title)
                                                                        <span
                                                                            class="upcoming-public-courses-location-title">({{ $location_title
                                                                                                                                                                                                                                                                            }})</span>
                                                                    @endif
                                                                </div>
                                                            </td>

                                                            @if (
                                                                    $courseDetails->field_choose_session_type_value ==
                                                                    config('app_constants.SESSION_TYPE_BROKEN_UP')
                                                                )
                                                                                            <td class="upcoming_courses_start_date">
                                                                                                @if(isset($courseDetails["field_start_date1_value"]))
                                                                                                                            @php
                                                                                                                                $date = strtotime($courseDetails["field_start_date1_value"]);
                                                                                                                                $month = date('F', $date);
                                                                                                                            @endphp

                                                                                                                            @if ($month == 'May')
                                                                                                                                <p>{{ date('M d, Y', $date) }}</p>
                                                                                                                            @else
                                                                                                                                <p>{{ date('M. d, Y', $date) }}</p>
                                                                                                                            @endif
                                                                                                @endif
                                                                                            </td>
                                                            @else
                                                                                    <td class="upcoming_courses_start_date">
                                                                                        @if(isset($courseDetails->field_session_dates_value2))
                                                                                                                    @php
                                                                                                                        $date = strtotime($courseDetails->field_session_dates_value);
                                                                                                                        $month = date('F', $date);
                                                                                                                    @endphp

                                                                                                                    @if ($month == 'May')
                                                                                                                        <p>{{ date('M d, Y', $date) }}</p>
                                                                                                                    @else
                                                                                                                        <p>{{ date('M. d, Y', $date) }}</p>
                                                                                                                    @endif
                                                                                        @endif
                                                                                    </td>
                                                            @endif

                                                            <td class="upcoming_courses_start_date">
                                                                @if ($courseDetails->field_choose_session_type_value == config('app_constants.SESSION_TYPE_BROKEN_UP'))
                                                                                                <?php
                                                                    $groupedTitles = [];

                                                                    for ($i = 1; $i <= 10; $i++) {
                                                                        if (isset($courseDetails["fieldDataFieldInstructor{$i}"]->{"fieldDataFieldInstructorNode{$i}"}->title)) {
                                                                            $instructorTitle = $courseDetails["fieldDataFieldInstructor{$i}"]->{"fieldDataFieldInstructorNode{$i}"}->title;

                                                                            if (!in_array($instructorTitle, $groupedTitles)) {
                                                                                $groupedTitles[] = $instructorTitle;
                                                                            }
                                                                        }
                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?>
                                                                                                @foreach ($groupedTitles as $title)
                                                                                                    <p>{{ $title }}</p>
                                                                                                @endforeach
                                                                @else
                                                                    {{ @$courseDetails->fieldDataFieldCourseInstructor->fieldCourseInstructorNode->title}}

                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ @$courseDetails->fieldDataFieldResaleNode->fieldDataFieldProceptSellTicketCourse->fieldDataFieldIfYesEventbriteLinkResale->field_if_yes_eventbrite_link_value }}"
                                                                    target="_blank" class="upcoming-public-courses-details">Details ></a>
                                                            </td>
                                                        </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination">
                        {{ $courseSessionsData->links('vendor.pagination.bootstrap-4') }}
                    </div>

                                                                </div></div>
            </div>
        </div>

    </div>
</section>
@include('layouts.advertisement')

@include('layouts.footer')
@endsection
