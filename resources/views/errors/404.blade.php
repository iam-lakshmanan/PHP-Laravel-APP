@extends('layouts.home')

@section('content')
<div class="d-flex align-items-center justify-content-center">
    <div class="text-center row mt-5">
        <div class=" col-md-12">
            <a href="/"><img src="{{ asset('/img/information/Procept Logo.png') }}" alt="Procept" width="260"
                    height="80" loading="lazy"></a>
        </div>
        <div class="col-md-12" style="padding: 67px">
            <p class="fs-3" style="font-size: 30px;"> <span class="text-word">Oops!</span> Page not found.</p>
            <p class="lead" style="font-size: 27px;
            padding: 25px;">
                The page you’re looking for doesn’t exist.
            </p>
        </div>
    </div>
</div>

@endsection
