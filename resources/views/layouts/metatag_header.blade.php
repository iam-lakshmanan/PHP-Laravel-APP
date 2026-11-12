@if (config('app_constants.PROCEPT_TITLE') == @$title)
    @section('title', config('app_constants.PROCEPT_MARKETING_TITLE'))
@else
    @section('title', @$title)
@endif
@section('description', @$description)
@section('abstract', @$abstract)
@section('keywords', @$keywords)
@section('og_title', @$og_title)
@section('og_type', @$og_type)
@section('og_url', @$og_url)
@section('og_site_name', @$og_site_name)
@section('og_determiner', @$og_determiner)
@section('og_description', @$og_description)
@section('og_image', @$og_image)
@section('og_updated_time', @$og_updated_time)
@section('article_published_time', @$article_published_time)
@section('shortlink', @$shortlink)
@section('twitter_card', @$twitter_card)
@section('twitter_url', @$twitter_url)
@section('twitter_title', @$twitter_title)
@section('twitter_description', @$twitter_description)



