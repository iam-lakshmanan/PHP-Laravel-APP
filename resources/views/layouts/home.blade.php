<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">


    <title>@yield('title', 'Procept Associates - Improving Your Project Delivery Excellence')</title>
    @if (View::hasSection('description') && trim(View::getSection('description')) !== '')
        <meta name="description" content="@yield('description')">
    @endif
    @if (View::hasSection('abstract') && trim(View::getSection('abstract')) !== '')
        <meta name="abstract" content="@yield('abstract')">
    @endif
    @if (View::hasSection('keywords') && trim(View::getSection('keywords')) !== '')
        <meta name="keywords" content="@yield('keywords')">
    @endif
    @if (View::hasSection('og_title') && trim(View::getSection('og_title')) !== '')
        <meta name="og:title" content="@yield('og_title')">
    @endif
    @if (View::hasSection('og_type') && trim(View::getSection('og_type')) !== '')
        <meta name="og:type" content="@yield('og_type')">
    @endif
    @if (View::hasSection('og_url') && trim(View::getSection('og_url')) !== '')
        <meta name="og:url" content="@yield('og_url')">
    @endif
    @if (View::hasSection('og_site_name') && trim(View::getSection('og_site_name')) !== '')
        <meta name="og:site_name" content="@yield('og_site_name')">
    @endif
    @if (View::hasSection('og_determiner') && trim(View::getSection('og_determiner')) !== '')
        <meta name="og:determiner" content="@yield('og_determiner')">
    @endif
    @if (View::hasSection('og_description') && trim(View::getSection('og_description')) !== '')
        <meta name="og:description" content="@yield('og_description')">
    @endif
    @if (View::hasSection('og_image') && trim(View::getSection('og_image')) !== '')
        <meta name="og:image" content="@yield('og_image')">
    @endif
    @if (View::hasSection('og_updated_time') && trim(View::getSection('og_updated_time')) !== '')
        <meta name="og:updated_time" content="@yield('og_updated_time')">
        <meta name="article:modified_time" content="@yield('og_updated_time')">
    @endif
    @if (View::hasSection('article_published_time') && trim(View::getSection('article_published_time')) !== '')
        <meta name="article:published_time" content="@yield('article_published_time')">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="shortlink" href="@yield('shortlink')">

    @if (View::hasSection('twitter_card') && trim(View::getSection('twitter_card')) !== '')
        <meta name="twitter:card" content="@yield('twitter_card')">
    @endif
    @if (View::hasSection('twitter_url') && trim(View::getSection('twitter_url')) !== '')
        <meta name="twitter:url" content="@yield('twitter_url')">
    @endif
    @if (View::hasSection('twitter_title') && trim(View::getSection('twitter_title')) !== '')
        <meta name="twitter:title" content="@yield('twitter_title')">
    @endif
    @if (View::hasSection('twitter_description') && trim(View::getSection('twitter_description')) !== '')
        <meta name="twitter:description" content="@yield('twitter_description')">
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" media="all">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>

    <link href="{{ asset('css/bootstrap-grid.css') }}" rel="stylesheet" media="all">

    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet" media="all">
 <link href="{{ asset('css/ads.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/owl.carousel.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/mystyle.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/all.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/alltestimonial.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/generic.css') }}" rel="stylesheet" media="all">

    <link href="{{ asset('css/font-details.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/home-page.css') }}" rel="stylesheet" media="all">

    <link href="{{ asset('css/courses.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/media-responsive.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/responsive.min.css') }}" rel="stylesheet" media="all">


@yield('content')
    <script src="{{ asset('js/imageurlhide.js') }}" async></script>
 <script src="{{ asset('js/coursetitle.js') }}" async></script>
    <script src="{{ asset('js/testimonials.js') }}" async></script>
    <script src="{{ asset('js/page-loader.js') }}" async></script>
    <script src="{{ asset('js/industry-recognition.js') }}" async></script>
    <script src="https://www.youtube.com/iframe_api" async></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.bundle.min.js" async></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" async></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" async></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

{{-- <script src="{{ asset('js/coursetitle.js') }}" async></script>
<script src="{{ asset('js/testimonials.js') }}" async></script>
<script src="{{ asset('js/page-loader.js') }}" async></script>
<script src="{{ asset('js/industry-recognition.js') }}" async></script>


<script src="https://www.youtube.com/iframe_api" defer></script>


<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.bundle.min.js" defer></script> --}}

