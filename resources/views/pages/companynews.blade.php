@extends('layouts.home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.page_nav')
<div class="container my-5">
    <div class="card-header">
        <h4 class="mb-0"><span class="allevents-title">View All </span> Company News</h4>
    </div>
    <button class="all-events-list mt-5">
        <a href="/"> Home </a> <i class='fas'>&#xf0da; </i> View All Company News
    </button>
    <div class="row">
        <div class="col-md-2">
            <div class="company-news-list">
                @foreach ($allCompanyNews as $item)
                    @if ($item->filename != '')
                        <p>
                            <a href="{{ route('getAllSlug', ['slug' => @$item->url]) }}">
                                <img src="{{ env('FILE_FOLDER') . '/' . $item->filename }}"
                                    class="menu-news-img mt-4" alt="View All Company News" loading="lazy">
                            </a>
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="col-md-10 mt-4">

            @foreach ($allCompanyNews as $item)
                <div class="company-news-list mb-4">
                    <a href="{{ route('getAllSlug', ['slug' => @$item->url]) }}"
                        class="company-news-title">
                        <h4 class="title-part-2">{{ $item->title ?? '' }}
                    </a></h4>

                    <div class="company-news-body-summary">
                        @if ($item->fieldDataBody->body_summary)
                            @if (strlen($item->fieldDataBody->body_summary) >= 250)
                                <span class="short-text">
                                    {!! substr($item->fieldDataBody->body_summary, 0, 250) !!}...</span>
                                <a href="{{ route('getAllSlug', ['slug' => @$item->url]) }}"
                                    class="company-list-see-more">See more</a>
                            @else
                                {!! $item->fieldDataBody->body_summary !!}
                            @endif
                        @else
                            @if (strlen($item->fieldDataBody->body_value) >= 250)
                                <span class="short-text">
                                    {!! substr($item->fieldDataBody->body_value, 0, 250) !!}...</span>
                                <a href="{{ route('getAllSlug', ['slug' => @$item->url]) }}"
                                    class="company-list-see-more">See more</a>
                            @else
                                {!! $item->fieldDataBody->body_value !!}
                            @endif
                        @endif


                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="pagination">
        {{ $allCompanyNews->links('vendor.pagination.bootstrap-4') }}
    </div>

</div>

@include('layouts.footer')
@endsection
