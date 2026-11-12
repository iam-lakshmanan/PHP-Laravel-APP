@extends('layouts.home')
@include('layouts.metatag_header')

@section('content')
@include('layouts.contact_header')
<section class="contact-us-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 mt-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @foreach ($contactInfoDetails as $content)
                    @if ($content->region == App\Constants\AppConstants::CONTENT)
                        <h2 class="contact-title">{{ $content->title }}</h2>
               {!! $content->body !!}
                    @endif
                @endforeach
                <div class="contact-us">
                    <form action="{{ route('contact.create') }}" method="POST" id="contactForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name">Your Name</label>
                                <input type="text" class="form-control contact-input" id="name" name="name">
                            </div>
                            <div class="col-md-12">
                                <label for="email">Your E-mail Address</label>
                                <input type="email" class="form-control contact-input" id="email" name="email">
                            </div>
                            <div class="col-md-12">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control contact-input" id="subject" name="subject"
                                    >
                            </div>
                            <div class="col-md-12">
                                <label for="message">Message</label>
                                <textarea class="form-control contact-input" id="message" name="message" rows="4"
                                    ></textarea>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                                @error('g-recaptcha-response')
                                <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn event-filter-btn btn-secondary">SEND
                                            MESSAGE</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="col-lg-5 contact-details mt-5">
                @foreach ($contactInfoDetails as $content)
                    @if ($content->region == App\Constants\AppConstants::RIGHTSIDEBAR)
                        <h2 class="contact-title">{{ $content->title }}</h2>
               {!! $content->body !!}
                    @endif
                @endforeach
            </div>
        </div>
        </div>
    {{-- </div> --}}
</section>
@include('layouts.footer')


@endsection
