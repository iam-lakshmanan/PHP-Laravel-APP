@extends('layouts.generic_page_home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.generic_header')
<section class="paying-us-section">
    <div class="container">
        <div class="row">
            <div class="col-12 mt-5  mb-5">
                <div class="paying-us-content">
                    <div class="row">
                        <div class="p-0">
                            {!! $beforeFirstTab !!}
                        </div>
                        <div class="col-2 paying-us-tab-ul p-md-0 p-lg-0 mt-4">
                            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                {!! $tabButtons !!}
                            </ul>
                        </div>
                        <div class="col-10 paying-us-tab-content-list mt-4">
                            <div class="tab-content paying-us-content-table" id="v-pills-tabContent">
                                {!! $tabContents !!}
                            </div>
                        </div>

                        <div class="p-0">
                            {!! @$afterLastTab !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@include('layouts.footer')
@endsection
