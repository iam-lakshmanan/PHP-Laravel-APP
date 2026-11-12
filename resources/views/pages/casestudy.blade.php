@extends('layouts.home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.page_nav')

<div class="row-12 text-left">
    @foreach ($casestudies as $casestudy)
        <div class="content text-left">
            <a href="{{ route('casestudy.show', ['nodeId' => $casestudy->nid, 'url' => $casestudy->url_alias]) }}"
                target="blank">{{ $casestudy->title }}</a>
        </div>
    @endforeach
</div>

@include('layouts.footer')
@endsection
