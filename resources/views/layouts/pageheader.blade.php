<header class="header-container top-header py-3 justify-content-between">
    <div class="row">
        <div class="col-2 m-auto">
            <a class="navbar-brand" href="{{ route('index')}}">
                <img src="{{ asset('/img/header_image/Procept Logo.png') }}" alt="Logo" width="260" height="80" loading="lazy">
            </a>
        </div>
        <div class="col-6 top-menu-bar justify-content-center align-items-center m-auto">

            @foreach($menuBarDetails as $menu)
                <nav class="menu-container">
                    <ul class="main-menu">
                        <li class="menu-item">
                            <a href="{{ isset($menu['url']) ? $menu['url'] : 'javascript:void(0);' }}"
                                class="{{ isset($menu['url']) && url()->current() == $menu['url'] ? 'active' : '' }} {{ isset($menu['url']) ? '' : 'menu-no-pointer' }}">
                                {{ $menu['title'] }}
                            </a>
                            @if (!empty($menu['submenus']))
                                <div class="mega-submenu">
                                    <ul class="sub-menu">
                                        @foreach($menu['submenus'] as $submenu)
                                            <li class="sub-menu-item">
                                                <a href="{{ $submenu['url'] ?? 'javascript:void(0);' }}"
                                                    class="{{ isset($submenu['url']) && url()->current() == $submenu['url'] ? 'active' : ''}} {{ isset($submenu['url']) ? '' : 'menu-no-pointer' }}">
                                                    {{ $submenu['title'] }}
                                                </a>
                                                @if (!empty($submenu['submenus']))
                                                    <ul class="nested-menu">
                                                        @foreach($submenu['submenus'] as $nestedMenu)
                                                            <li class="nested-menu-item">
                                                                <a href="{{ $nestedMenu['url'] ?? 'javascript:void(0);' }}"
                                                                    class="{{isset($nestedMenu['url']) && url()->current() == $nestedMenu['url'] ? 'active' : '' }} {{ isset($nestedMenu['url']) ? '' : 'menu-no-pointer' }}">
                                                                    {{ $nestedMenu['title'] }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    </ul>
                </nav>
            @endforeach




        </div>

        <div class="col-3  flex justify-content-end d-flex row  align-items-center">
            <div class="training-contact-info align-items-center ">
                <div class="phone-number m-auto">
                    <a href="tel:1-800-261-6861" class="contact-info-link">1-800-261-6861</a>
                </div>
                <div class="phone-number m-auto">
                    <a class="contact-info-link"> |</a>
                </div>
                <div class="mail-logo m-auto">
                    <a href="mailto:info@procept.com" class="contact-info-link">info@procept.com</a>
                </div>
            </div>
        </div>

        <div class="col-1 justify-content-center align-items-center m-auto">
            <div class="training-social-links text-center ">
                <div class="social-icons  justify-content-around d-flex">
                    <a href="https://www.youtube.com/channel/UCtI42RRQ_3lpI8t2Ah1Zthw" target="_blank">
                        <img src="{{ asset('/img/header_image/social_media_youTube_icon.png') }}" alt="YouTube Icon"
                            width="24" height="50" loading="lazy">
                    </a>
                    <a href="https://www.linkedin.com/company/121609/ " target="_blank">
                        <img src="{{ asset('/img/header_image/social_media_linkedIn_icon.png') }}" alt="LinkedIn Icon"
                            width="16" height="50" loading="lazy">
                    </a>



                    <a href="{{ route('rss.feed') }}" target="_blank">

                        <img src="{{ asset('/img/header_image/social_media_RSS_icon.png') }}" alt="RSS Icon" width="16"
                            height="30" loading="lazy">
                    </a>
                </div>
            </div>
        </div>

        <div class="course-level search-container1">
            <div class="col-md-10 d-flex training-search-box">
                <input placeholder="Search site" class="form-control border-0 page-search-courses">
                <img src="{{ asset('/img/search_and_video/Seach icon.png') }}" alt="search-icon"
                    class="img-fluid training-search-icon page-search-courses" loading="lazy">
            </div>

            <div class="filter-page-courses-container1">
                <div id="filter-page-courses-container" class="container">
                    <ul id="filter-courses-list1" class="list-unstyled">
                        <!-- Your search results go here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="course-message-bar justify-content-center align-items-center">
    <marquee>
        @foreach ($promotionalMessageBar as $promotional)
            @if (!empty($promotional->url))
                <a href="{{ $promotional->url }}" class="message-link" target="_blank">
                    <p style="display:inline;" class="message-text">{{ $promotional->message }} </p>
                </a>
            @else
                <p style="display:inline;">{{ $promotional->message }}</p>
            @endif
        @endforeach
    </marquee>
</div>

<div class="video-banner-container">
    <div class="training-video-banner">
        <video autoplay muted loop class="video-height">
            <source src="{{asset('/img/4426529-uhd_3840_2160_25fps.mp4')}}" type="video/mp4" class="d-block w-100">
            <track src="{{ asset('/captions/captions.vtt') }}" kind="captions" srclang="en" label="English">
            Your browser does not support the video tag.
        </video>
        <div class="overlay training-text-container">
            <div class="container training-management-course">
                <div class="row">

                    <div class="col-9 training-content">
                        <h2 class="mb-3">{{ @$courseData->title }}</h2>
                        <p>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="course-navigation d-none">
    <input type="hidden" id="selected-nid">
    <input type="hidden" id="selected-node-type">
    <input type="hidden" id="selected-page-nid">

    <a href="" id="course-summary">
    </a>

</div>
</header>
