<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
<style>
    /* Initial state */
    .menu-item {
        position: relative;
        color: #00528a;
        text-decoration: none;
    }

    .menu-item::before {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background-color: #00528a;
        transition: width 0.4s ease-in-out;
        /* Smooth transition for width */
    }

    /* Hover state */
    .menu-item:hover::before {
        width: 100%;
        /* Full width on hover */
    }

    .menu-item:hover {
        color: #00528a;
        /* Change color on hover */
    }


    .dropdown-submenu {
        position: relative;
    }

    .dropdown-menu {
        display: none;
    }

    .dropdown-submenu:hover>.dropdown-menu {
        display: block;
    }

    .dropdown-submenu .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -6px;
    }

    /* Message Bar Styles */
    .message-bar {
        background-color: #0099ff;
        padding: 10px 0;
        border-top: 1px solid #e9ecef;
    }

    .dropdown-submenu:hover .nestedMenu {
        display: block;
    }

    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu:hover>.dropdown-menu {
        display: block;
    }

    .dropdown-menu {
        display: none;
    }

    .dropdown-submenu:hover>.nestedMenu {
        display: block;
    }

    .nestedMenu {
        position: absolute;
        left: 100%;
        top: 0;
        box-shadow: 0px 0px 15px 0px #00548E;
        border: none;
        background: white;
        border-radius: 5px;
        padding: 3%;
        z-index: 99999 !important;

        display: none;
    }

    .dropdown-submenu:hover .nestedMenu {
        display: block;
    }

    .w-82 {
        width: 82%;
    }

    @media (max-width: 768px) {
        .contact-info {
            gap: 1rem;
            /* Reduce the gap on smaller screens */
        }

        .social-connect {
            margin-left: 1rem;
            /* Adjust margin on smaller screens */
        }
    }

    .scrolling-text-wrapper {
        white-space: nowrap;
        position: relative;

    }

    .scrolling-text {
        display: inline-block;
        padding-left: 100%;
        animation: scroll-left 30s linear infinite;
    }

    @keyframes scroll-left {
        from {
            transform: translateX(0%);
        }

        to {
            transform: translateX(-100%);
        }
    }

.body-summary {
    color: #ffffff;
    font-size: 1rem;
}


/* Maintain your existing see-more styles */
.see-more-link {
    color: #0066cc;
    text-decoration: underline;
    margin-left: 4px;
}

.company-news-item {
    padding-bottom: 1rem;
}

.footer-content p,
.footer-content li {
    all: unset;
    display: inline;
}

.footer-content {
    color: inherit;
    line-height: 23px;
}
.footer-content span a {
    color: #ffffff;
}
.footer-content p > span {
    font-weight: bold;
}

.footer-content a::before {
    content: " | ";
    color: #fff;

}

.footer-content a {
    color: #fff;
    font-weight: bold;
    text-decoration: underline;
}

</style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <div class="row mx-auto">
                <!-- Logo Section and Toggler for Mobile View -->
                <div
                    class="col-lg-3 col-md-12 d-flex align-items-center justify-content-lg-center justify-content-md-between">
                    <a class="navbar-brand logo_nav" href="{{ route('index') }}">
                        <img src="{{ asset('/img/header_image/ProceptLog.webp') }}" class="" alt="Logo" loading="lazy">
                    </a>
                    <!-- Mobile Toggle Button -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- Centered Menu -->
                <div class="col-lg-5 col-md-9 d-flex align-items-center justify-content-center">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav d-flex align-items-center justify-content-center mx-auto">
                            @foreach ($menuBarDetails as $index => $menu)
                                @php
                                    $dropdownIdDesktop = 'navbarDropdown-' . $index . '-desktop';
                                    $dropdownIdMobile = 'navbarDropdown-' . $index . '-mobile';

                                    // Resolve menu link
                                    $parts = explode('/', $menu['link_path'] ?? '');
                                    if (
                                        !empty($menu['link_path']) &&
                                        in_array($menu['link_path'], [
                                            config('app_constants.NOLINK'),
                                            config('app_constants.DROPLINK'),
                                        ])
                                    ) {
                                        $menu_link = 'javascript:void(0)';
                                        $menu_link_pointer = 'menu-no-pointer';
                                    } elseif (!empty($parts[0]) && $parts[0] === 'node') {
                                        $menu_link = url($menu['link_url']);
                                        $menu_link_pointer = '';
                                    } else {
                                        $menu_link = $menu['link_path'] ?? '#';
                                        $menu_link_pointer = '';
                                    }
                                @endphp

                                <li class="nav-item @if (!empty($menu['subMenu'])) dropdown @endif nav_space w-100">
                                    @if (!empty($menu['link_path']))
                                        <!-- Desktop View -->
                                        <a href="{{ $menu_link }}"
                                            class="nav-link d-flex align-items-center menu-item d-none d-md-flex {{ @$menu_link_pointer }}"
                                            id="{{ $dropdownIdDesktop }}">
                                            {{ $menu['link_title'] }}
                                        </a>
                                        <!-- Mobile View -->
                                        <a href="#"
                                            class="nav-link d-flex align-items-center menu-item d-flex d-md-none"
                                            id="{{ $dropdownIdMobile }}" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $menu['link_title'] }}
                                        </a>
                                    @else
                                        <span class="nav-link d-flex align-items-center menu-item">
                                            {{ $menu['link_title'] }}
                                        </span>
                                    @endif

                                    @if (isset($menu['subMenu']) && count($menu['subMenu']) > 0)
                                        <ul class="dropdown-menu" aria-labelledby="{{ $dropdownIdMobile }}">
                                            <li class="dropdown-submenu">
                                                @if (!empty($menu['link_path']))
                                                    @php
                                                        $parts = explode('/', $menu['link_path']);
                                                        if (
                                                            in_array($menu['link_path'], [
                                                                config('app_constants.NOLINK'),
                                                                config('app_constants.DROPLINK'),
                                                            ])
                                                        ) {
                                                            $menu_data_link = 'javascript:void(0)';
                                                            $menu_data_link_pointer = 'menu-no-pointer';
                                                        } elseif ('node' == $parts[0]) {
                                                            $menu_data_link = url($menu['link_url']);
                                                            $menu_data_link_pointer = '';
                                                        } else {
                                                            $menu_data_link = $menu['link_path'];
                                                            $menu_data_link_pointer = '';
                                                        }
                                                    @endphp
                                                    <a href="{{ $menu_data_link }}"
                                                        class="dropdown-item menu-title d-lg-none d-sm-block {{ @$menu_data_link_pointer }}">
                                                        {{ $menu['link_title'] }}
                                                    </a>
                                                @else
                                                    <span class="dropdown-item menu-title d-lg-none d-sm-block">
                                                        {{ $menu['link_title'] }}
                                                    </span>
                                                @endif
                                                <hr class="dropdown-divider d-lg-none d-sm-block">
                                            </li>
                                            @foreach ($menu['subMenu'] as $submenu)
                                                <li class="dropdown-submenu">
                                                    @php
                                                        $parts = explode('/', $submenu['link_path']);
                                                        if (
                                                            in_array($submenu['link_path'], [
                                                                config('app_constants.NOLINK'),
                                                                config('app_constants.DROPLINK'),
                                                            ])
                                                        ) {
                                                            $submenu_link = 'javascript:void(0)';
                                                            $submenu_link_pointer = 'menu-no-pointer';
                                                        } elseif ('node' == $parts[0]) {
                                                            $submenu_link = url($submenu['link_url']);
                                                            $submenu_link_pointer = '';
                                                        } else {
                                                            $submenu_link = $submenu['link_path'];
                                                            $submenu_link_pointer = '';
                                                        }
                                                    @endphp

                                                    <a href="{{ $submenu_link }}"
                                                        class="dropdown-item {{ @$submenu_link_pointer }}">

                                                        <i class="fas fa-chevron-right me-2"></i>
                                                        {{ $submenu['link_title'] }}
                                                    </a>
                                                    @if (isset($submenu['megaMenu']) && count($submenu['megaMenu']) > 0)
                                                        <ul class="nestedMenu dropdown-submenu">
                                                            @foreach ($submenu['megaMenu'] as $nestedMenu)
                                                                <li>
                                                                    @php
                                                                        $parts = explode('/', $nestedMenu['link_path']);
                                                                        if (
                                                                            in_array($nestedMenu['link_path'], [
                                                                                config('app_constants.NOLINK'),
                                                                                config('app_constants.DROPLINK'),
                                                                            ])
                                                                        ) {
                                                                            $nestedMenu_link = 'javascript:void(0)';
                                                                            $nestedMenu_pointer = 'menu-no-pointer';
                                                                        } elseif ('node' == $parts[0]) {
                                                                            $nestedMenu_link = url(
                                                                                $nestedMenu['link_url'],
                                                                            );
                                                                            $nestedMenu_pointer = '';
                                                                        } else {
                                                                            $nestedMenu_link = $nestedMenu['link_path'];
                                                                            $nestedMenu_pointer = '';
                                                                        }
                                                                    @endphp
                                                                    <a href="{{ $nestedMenu_link }}"
                                                                        class="dropdown-item {{ isset($nestedMenu['link_path']) ? '' : 'menu-no-pointer' }}">
                                                                        <i class="fas fa-arrow-right me-2"></i>
                                                                        {{ $nestedMenu['link_title'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div> <!-- End collapse -->
                </div>


                <!-- Contact Info and Social Links -->
                <div class="col-lg-4 col-md-4 my-auto">
                    <div
                        class="d-flex flex-wrap flex-md-nowrap align-items-center justify-content-center w-100">
                        <!-- Contact Info Section -->
                        <div class="contact-info d-flex flex-nowrap align-items-center gap-2">
                            <a href="tel:1-800-261-6861" class="contact-info-link">1-800-261-6861</a>
                            <span class="d-none d-md-inline">|</span>
                            <a href="mailto:info@procept.com" class="contact-info-link">info@procept.com</a>
                        </div>

                        <!-- Social Media Section -->
                        <div class="social-connect d-flex align-items-center gap-3 ms-3">
                            <a href="https://www.youtube.com/channel/UCtI42RRQ_3lpI8t2Ah1Zthw" target="_blank"
                                aria-label="YouTube">
                                <img src="{{ asset('/img/header_image/social_media_youTube_icon.png') }}"
                                    alt="YouTube Icon" class="img-fluid" width="48" height="48" loading="lazy">
                            </a>
                            <a href="https://www.linkedin.com/company/121609/" target="_blank" aria-label="LinkedIn">
                                <img src="{{ asset('/img/header_image/social_media_linkedIn_icon.png') }}"
                                    alt="LinkedIn Icon" class="img-fluid" width="48" height="48" loading="lazy">
                            </a>
                            <a href="{{ route('rss.feed') }}" target="_blank" aria-label="RSS Feed">
                                <img src="{{ asset('/img/header_image/social_media_RSS_icon.png') }}" alt="RSS Icon"
                                    class="img-fluid" width="48" height="48" loading="lazy">
                            </a>
                        </div>
                    </div>

                    <!-- Search Container -->
                    <div class="search-container d-flex justify-content-center page-nav-search">
                        <div class="d-flex align-items-center border  search-input-bar">
                            <input placeholder="Search site" class="form-control border-0 page-search-courses me-2 header-page-menu">
                            <img src="{{ asset('/img/search_and_video/search_icon.png') }}" alt="search-icon"
                                class="img-fluid training-search-icon page-search-courses" loading="lazy">
                        </div>
                        <div class="filter-page-courses-container" style="display: none;">
                            <div id="filter-page-courses-container" class="container" style="display: none;">
                                <ul id="filter-courses-list" class="list-unstyled" style="display: none;">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Message Bar -->
    <div class="message-bar d-flex justify-content-center align-items-center col-12">
        <div class="scrolling-text-wrapper w-100 overflow-hidden">
            <div class="scrolling-text d-inline-block">
                @foreach ($promotionalMessageBar as $promotional)
                    @if (!empty($promotional->url))
                        <a href="{{ $promotional->url }}" class="message-link d-inline-block me-4" target="_blank">
                            <span class="message-text">{{ $promotional->message }}</span>
                        </a>
                    @else
                        <span class="message-text me-4">{{ $promotional->message }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- JavaScript for Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
    <div class="course-navigation d-none">
        <input type="hidden" id="selected-nid">
        <input type="hidden" id="selected-node-type">
        <input type="hidden" id="selected-page-nid">

        <a href="" id="course-summary">
        </a>

    </div>
