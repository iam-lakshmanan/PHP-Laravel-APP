@extends('layouts.home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.page_nav')
@include('layouts.consultants_header')

<section id="our-consultants-section" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="filter-section d-flex flex-column flex-md-row align-items-center mb-4">
                    <p class="h5 mb-3 me-md-3 mb-md-0">Filter:</p>
                    <ul class="nav nav-pills mb-0 d-flex flex-wrap justify-content-center justify-content-md-start align-items-center"
                        id="pills-tab" role="tablist">
                        @foreach ($consultants_type_details as $key => $consultants_item)
                            <li class="nav-item me-2 mb-2 mb-md-0" role="presentation">
                                <a id="{{ strtolower(str_replace(' ', '-', $consultants_item['consultants_type'])) }}-tab"
                                    class="nav-link {{ $current_tab == strtolower(str_replace(' ', '-', $consultants_item['consultants_type'])) ? 'active' : '' }}"
                                    href="{{ route('our.consultants.page', ['consultant_tab' => strtolower(str_replace(' ', '-', $consultants_item['consultants_type']))]) }}"
                                    role="tab"
                                    aria-selected="{{ $current_tab == strtolower(str_replace(' ', '-', $consultants_item['consultants_type'])) ? 'true' : 'false' }}">
                                    {{ $consultants_item['consultants_type'] === config('app_constants.OUR_CONSULTANT') ? config('app_constants.ALL_CONSULTANT') : $consultants_item['consultants_type'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>


                </div>
            </div>
        </div>

        <div class="row">
            <div class="tab-content mt-5" id="pills-tabContent">
                @foreach ($consultants_type_details as $key => $consultants_item)
                                <div class="tab-pane fade {{ $current_tab == strtolower(str_replace(' ', '-', $consultants_item['consultants_type'])) ? 'show active' : '' }}"
                                    id="pills-{{ str($consultants_item['consultants_type'])->lower()->replace(' ', '-') }}"
                                    role="tabpanel">
                                    <div class="container">
                                        <div class="row">
                                            <!-- Consultant Profile Loop -->
                                            @foreach ($consultants_item['consultants_details'] as $item_details)
                                                                <div class="col-6 col-md-4 col-lg-1-5 mb-4">
                                                                    <div class="consultant-profile text-center">
                                                                        @if (@$item_details->filename != '')
                                                                                                    @php
                                                                                                        $imageUrl = env('TEAM_FILE_FOLDER') . '/' .
                                                                                                            @$item_details->filename;
                                                                                                        if ($item_details->uri) {
                                                                                                            $pathParts = explode(config('app_constants.PUBLIC_FOLDER'), $item_details->uri);
                                                                                                            $relativePath = isset($pathParts[1]) ? $pathParts[1] : $item_details->filename;
                                                                                                            $imageUrl = env('TEAM_CONSULTANTS_FILE_FOLDER') . '/' .
                                                                                                                $relativePath;
                                                                                                        }
                                                                                                    @endphp

                                                <a href="{{ route('getAllSlug', ['slug' => $item_details->team_url]) }}">
                                                    <img src="{{ asset(@$imageUrl) }}"
                                                         alt="{{ @$item_details->title }}"
                                                         class="mb-2 consultant-image"
                                                         loading="lazy">
                                                </a>
                                            @endif

                                            @if (isset($item_details->title))
                                                <h5 class="me-2">
                                                    <a href="{{ route('getAllSlug', ['slug' => $item_details->team_url]) }}">
                                                        {{ $item_details->title }}
                                                    </a>
                                                </h5>
                                            @endif

                                                                        @if (isset($item_details->field_credentials_value))
                                                                            <p class="consultant-credentials mb-2 px-4 me-2">
                                                                                {{ @$item_details->field_credentials_value }}
                                                                            </p>
                                                                        @endif
                                                                        @if (isset($item_details->field_team_position_value))
                                                                            <p class="consultant-position mb-2 px-4 me-2">
                                                                                {{ @$item_details->field_team_position_value }}
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="pagination">
                                        {{ $consultants_item['consultants_details']->appends([
                        'tab' =>
                            request('tab')
                    ])->links('vendor.pagination.bootstrap-4') }}
                                    </div>
                                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@include('layouts.footer')
@endsection
