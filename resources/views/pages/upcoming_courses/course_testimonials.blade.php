@extends('layouts.home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.page_nav')
<div class="container mt-5">
    <div class="alltestimonial-header">
        <h4 class="mb-0"><span class="alltestimonial-title">Course </span> Testimonials</h4>
    </div>
    <button class="alltestimonial-list mt-5">
        <a href="/"> Home </a> <i class='fas'>&#xf0da; </i> Course Testimonials
    </button>
</div>
<section class="consultants-alltestimonials">
    <div class="alltestimonials container py-5">
        <div class="row d-flex justify-content-center">
            @foreach ($courseTestimonials as $alltestimonials)
                        <div class="col-12 mb-4 alltestimonial-content">
                            <div class="alltestimonial-box p-4">
                                <div class="row align-items-start">
                                    <div class="col-1">
                                        @php
                                            $alltestimonialImage =
                                                @$alltestimonials->FieldDataFieldTestimonialImage->FileManagedTestimonialImage->uri;
                                            if ($alltestimonialImage) {
                                                $pathParts = explode(config('app_constants.PUBLIC_FOLDER'), $alltestimonialImage);
                                                $relativePath = isset($pathParts[1]) ? $pathParts[1] : $alltestimonialImage;
                                                $imageUrl = env('FILE_FOLDER') . '/' . $relativePath;
                                            } else {
                                                $imageUrl = asset('/img/upcoming_public_courses/user.jpg');
                                            }
                                        @endphp
                                        <img src="{{ $imageUrl }}" alt="allTestimonial Image"
                                            class="w-70 allconsultant-testimonial-img" loading="lazy">

                                    </div>
                                    <div class="col-11">
                                        @php
                                            $bodyText = strip_tags($alltestimonials->FieldDataBodyCourse->body_value);
                                        @endphp
                                        <div class="d-flex flex-column flex-grow-1">
                                            <p class="alltestimonial-text mb-0 me-auto">"{!!
                                     strip_tags($alltestimonials->FieldDataBodyCourse->body_value) !!}"</p>
                                            <div class="allconsultants-team-position text-end">
                                                <p class="mb-0 text-end mt-2"><b>{{ @$alltestimonials->FieldDataFieldAuthor->field_author_value }}</b>: {{
                    @$alltestimonials->FieldDataFieldTestimonialPositionNode->field_testimonial_position_value}}</p>
                                            </div>
                                            <div class="quotes text-end">
                                                <i class="fas fa-quote-right custom-quote-icon"></i>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>


                        </div>
            @endforeach
        </div>
        <div class="pagination d-flex justify-content-center">
            {{ $courseTestimonials->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>



</section>




@include('layouts.footer')
@endsection
