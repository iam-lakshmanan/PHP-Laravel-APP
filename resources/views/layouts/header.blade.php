<div class="video-banner-container">
    <div class="video-banner">
        <video autoplay muted loop preload="metadata" class="video-height w-100">
            <source src="{{ asset('/img/4426529-uhd_3840_2160_25fps.mp4') }}" type="video/mp4" class="d-block w-100">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="container-fluid main-content search-filter home-search-box mt-0 p-0">
        <div class="search-bar py-5 px-4 d-flex justify-content-center">
            <div class="row g-1 py-0">
                <div class="search-container mb-3 col-12">
                    <div class="col-12 col-md-10 d-flex home-search-box">
                        <input placeholder="What do you want to learn?" class="form-control border-0 search-courses">
                        <img src="{{ asset('/img/search_and_video/search_icon.png') }}" alt="search-icon"
                            class="img-fluid search-icon search-courses" loading="lazy">
                    </div>
                    <div class="filter-courses-container" style="display: none;">
                        <div id="filter-courses-container" class="container" style="display: none;">
                            <ul id="filter-courses-list-one" class="list-unstyled" style="display: none;">
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="menu-list menu-list-content col-12">
                    <div class="row">
                      

                        <div class="menu-list-container d-flex justify-content-center align-items-center  flex-wrap text-center w-100">
                            <div class="d-md-flex align-items-md-center">
                                <div class="search-bar-menu-list px-2">
                                    <h5 class="mb-0 menu-info-title d-inline">1+ million trained</h5>
                                </div>
                                <span class="menu-info-link mx-2">|</span>
                                <div class="search-bar-menu-list px-2">
                                    <h5 class="mb-0 menu-info-title d-inline">17,000+ clients</h5>
                                </div>
                                <span class="menu-info-link mx-2">|</span>
                                <div class="search-bar-menu-list px-2">
                                    <h5 class="mb-0 menu-info-title d-inline">70+ instructors</h5>
                                </div>
                                <span class="menu-info-link mx-2">|</span>
                                <div class="search-bar-menu-list px-2">
                                    <h5 class="mb-0 menu-info-title d-inline">300+ courses</h5>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
        <div class="course-navigation d-none">
            <input type="hidden" id="selected-nid-one">
            <a href="" id="course-summary-change"></a>
        </div>
    </div>
</div>
