@extends('layouts.generic_page_home')
@include('layouts.metatag_header')
@section('content')
    @include('layouts.consulting_header')
    <section class="consulting-landing-page-section">
        {!! $pageBodyContent !!}
    </section>
    <div class="mt-4">
    @include('layouts.footer')
    </div>
@endsection


























