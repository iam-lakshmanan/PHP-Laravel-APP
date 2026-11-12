<section id="advertisement-courses-job">
    <div class="container-fluid">
        <div class="row advertisement">
            <!-- First Advertisement (Canada Job Grant) -->
            <div class="col-12 col-md-6 mb-0 px-0 position-relative">
                <img src="{{ asset('/img/training_ads/canada_job_ad.jpg') }}" alt="Canada Job Advertisement"
                    class="img-fluid" loading="lazy">
                <div class="canda-ad-content canada-ad-content-one">
                    <h6 class="fw-bold text-center">CANADA JOB GRANTS</h6>
                    <p class="mx-auto">Unlock substantial savings on employee training!<br>
                        Get 50-100% of training costs covered.
                    </p>
                    <a href="{{ url(config('app_constants.CANADA_JOB_GRANT_URL')) }}" class="jobs-ad-btn">
                        <img src="{{ asset('img/services_image/green_learn_more_button.png') }}" class="jobs-learn-more"
                            alt="Learn More">
                    </a>
                </div>
            </div>

            <!-- Second Advertisement (Newcomer to Canada) -->
            <div class="col-12 col-md-6 mb-0 px-0 position-relative">
                <img src="{{ asset('/img/training_ads/newcomer_gant_ads.png') }}" alt="New Comer Advertisement"
                    class="img-fluid" loading="lazy">
                <div class="canda-ad-content canada-ad-content-two">
                    <h6 class="fw-bold">NEWCOMER<br> TO CANADA?</h6>
                    <p class="mx-auto text-left text-dark">We can help arrange grants and/or<br> low-interest
                        loans to cover
                        training <br>costs for your
                        next promotion or career switch.
                    </p>
                    <a href="{{ url(config('app_constants.FINACING_TRAINING_IMMIGRANTS_URL')) }}" class="canada-ad-btn">
                        <img src="{{ asset('img/services_image/dark_blue_learn_more.svg') }}" class="canada-learn-more"
                            alt="Learn More">
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
