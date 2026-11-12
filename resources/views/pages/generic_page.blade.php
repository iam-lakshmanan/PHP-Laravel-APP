@php
    use App\Constants\AppConstants;
    use Carbon\Carbon;
@endphp

@extends('layouts.generic_page_home')
@include('layouts.metatag_header')
@section('content')
    @include('layouts.generic_header')

    <section class="generic-page-section">
        <div class="container">
            <div class="row">
                <div class="col-12 mt-2 py-5 px-md-0 px-lg-0 mx-lg-0 mx-md-0 px-4 mx-2">
                    <div class="generic-content">
                        @php
                            $content = $page_content->body_value ?? '';
                            $baseUrl = env('TRAINING_URL');


                            $liveUrl = env('PROCEPT_COM');
                            $trainingUrl = env('TRAINING_URL');

                            $content = preg_replace(
                                '/https?:\/\/(' .
                                    preg_quote($liveUrl, '/') .
                                    '|' .
                                    preg_quote($trainingUrl, '/') .
                                    ')/',
                                '',
                                $content,
                            );

                            $updatedContent = preg_replace_callback(
                                '/<img\s+[^>]*src="([^"]*)"/i',
                                function ($matches) use ($baseUrl) {
                                    $src = $matches[1];
                                    if (!preg_match('/^https?:\/\//i', $src)) {
                                        $src = $baseUrl . ltrim($src, '/');
                                    }
                                    return str_replace($matches[1], $src, $matches[0]);
                                },
                                $content,
                            );

                            $eventDateTimeHtml = '';
                            if (
                                isset($page_content->type) &&
                                $page_content->type === AppConstants::NODETYPE_EVENTS &&
                                !empty($page_content->field_event_date_value)
                            ) {
                                $eventStart = Carbon::parse($page_content->field_event_date_value);
                                $eventEnd = !empty($page_content->field_event_date_value2)
                                    ? Carbon::parse($page_content->field_event_date_value2)
                                    : null;

                                $eventDateTimeHtml = '<p class="event-date-time" style="text-align: left;">' .
                                    '<strong>Event Date &amp; Time:</strong> ' .
                                    e($eventStart->format('l, F j, Y')) . ' at ' . e($eventStart->format('g:i A'));
                                if ($eventEnd) {
                                    $eventDateTimeHtml .= ' &ndash; ' . (
                                        $eventEnd->isSameDay($eventStart)
                                            ? e($eventEnd->format('g:i A'))
                                            : e($eventEnd->format('l, F j, Y')) . ' at ' . e($eventEnd->format('g:i A'))
                                    );
                                }
                                $eventDateTimeHtml .= ' Eastern time zone</p>';

                                $contentWithEventDateTime = preg_replace(
                                    '/(?=<(?:button|a)\b[^>]*>(?:(?!<\/(?:button|a)>)[\s\S])*?\bregister(?:\s|&nbsp;)+now\b)/i',
                                    $eventDateTimeHtml,
                                    $updatedContent,
                                    1,
                                );

                                if ($contentWithEventDateTime !== null && $contentWithEventDateTime !== $updatedContent) {
                                    $updatedContent = $contentWithEventDateTime;
                                    $eventDateTimeHtml = '';
                                }
                            }
                        @endphp

                        {!! preg_replace('/<\/?strong>/', '', \Shortcode::compile($updatedContent)) !!}

                        @if ($eventDateTimeHtml !== '')
                            {!! $eventDateTimeHtml !!}
                        @endif


                        @if (isset($page_content->block_custom_details) &&
                                $page_content->block_custom_details->region == config('app_constants.REGION_USER_00'))
                            <div class="generic-content-user-00">
                                <div class="container">
                                    <div class="row">
                                        @if (count($page_content->meet_our_management_team) > 0)
                                            <hr>
                                            <!-- Center the title -->
                                            <h2 class="mt-2 mb-3 text-center">
                                                {{ @$page_content->block_custom_details->title }}
                                            </h2>

                                            @foreach ($page_content->meet_our_management_team as $management_item)
                                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                                    <div
                                                        class="generic-user-00-image d-flex justify-content-center align-items-center">
                                                        @php
                                                            $imageUrl =env('TEAM_FILE_FOLDER') . '/' .
                                                                @$management_item->filename;
                                                            if ($management_item->uri) {
                                                                $pathParts = explode(
                                                                    config('app_constants.PUBLIC_FOLDER'),
                                                                    $management_item->uri,
                                                                );
                                                                $relativePath = isset($pathParts[1])
                                                                    ? $pathParts[1]
                                                                    : $management_item->filename;
                                                                $imageUrl =
                                                                    env('TEAM_CONSULTANTS_FILE_FOLDER') .
                                                                    '/' .
                                                                    $relativePath;
                                                            }
                                                        @endphp
                                                        <img src="{{ asset(@$imageUrl) }}"
                                                            alt="{{ @$management_item->title }}"
                                                            class="mb-2 consultant-image me-2 img-fluid" loading="lazy">
                                                    </div>

                                                    <div class="generic-user-00-title text-center">
                                                        <p class="management-item-title">{{ $management_item->title }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('layouts.footer')
@endsection
