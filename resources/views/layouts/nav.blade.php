<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
<style>
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
    }

    .menu-item:hover::before {
        width: 100%;
    }

    .menu-item:hover {
        color: #00528a;
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

    @media only screen and (min-width: 768px) and (max-width: 1023px) {
        .contact-info {
            font-size: 12px !important;

        }
    }

    .contact-info-link {
        white-space: nowrap;
    }

    .contact-info,
    .social-connect {
        flex-wrap: nowrap;
    }

    @media (max-width: 768px) {
        .contact-info {
            gap: 1rem;
        }

        .social-connect {
            margin-left: 1rem;
        }
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
                        <img src="{{ asset('/img/header_image/Procept Logo.png') }}" loading="lazy" class="" alt="Logo">
                    </a>
                    <!-- Mobile Toggle Button -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- Centered Menu -->
                <div class="col-lg-6 col-md-9 d-flex align-items-center justify-content-center">
                    <div class="collapse navbar-collapse" id="navbarNav">

                        <ul class="navbar-nav d-flex align-items-center justify-content-center mx-auto">
                            @foreach ($menuBarDetails as $menu)
                                <li class="nav-item @if (!empty($menu['subMenu'])) dropdown @endif nav_space w-100">
                                    @if (!empty($menu['link_path']))
                                        <!-- Desktop View -->
                                        @php
                                        $parts = explode('/', $menu['link_path']);

                                        if (in_array($menu['link_path'], [config('app_constants.NOLINK'), config('app_constants.DROPLINK')])) {
                                            $menu_link = 'javascript:void(0)';
                                            $menu_no_pointer='menu-no-pointer';
                                        } elseif ('node' == $parts[0]) {
                                            $menu_link = url($menu['link_url']);
                                            $menu_no_pointer='';

                                        } else {
                                            $menu_link = $menu['link_path'];
                                            $menu_no_pointer='';

                                        }
                                     @endphp
                                        <a href="{{ $menu_link }}"
                                        class="nav-link d-flex align-items-center menu-item d-none d-md-flex {{ @$menu_no_pointer }}"
                                        id="desktop-content">
                                        {{ $menu['link_title'] }}
                                    </a>

                                        <!-- Mobile View -->
                                        <a href="javascript:void(0);"
                                            class="nav-link d-flex align-items-center menu-item d-flex d-md-none menu-no-pointer"
                                            id="mobile-content" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $menu['link_title'] }}
                                        </a>
                                    @else
                                        <span class="nav-link d-flex align-items-center menu-item">
                                            {{ $menu['link_title'] }}
                                        </span>
                                    @endif

                                    @if (isset($menu['subMenu']) && count($menu['subMenu']) > 0)
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <!-- Copy of Main Menu Title in Submenu -->
                                            <li class="dropdown-submenu">
                                                @if (!empty($menu['link_path']))

                                                @php
                                                $parts = explode('/', $menu['link_path']);

                                                if (in_array($menu['link_path'], [config('app_constants.NOLINK'), config('app_constants.DROPLINK')])) {
                                                    $menu_data_link = 'javascript:void(0)';
                                                    $menu_data_link_pointer='menu-no-pointer';
                                                } elseif ('node' == $parts[0]) {
                                                    $menu_data_link = url($menu['link_url']);
                                                    $menu_data_link_pointer='';
                                                } else {
                                                    $menu_data_link = $menu['link_path'];
                                                    $menu_data_link_pointer='';
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

                                                    if (in_array($submenu['link_path'], [config('app_constants.NOLINK'), config('app_constants.DROPLINK')])) {
                                                        $submenulink = 'javascript:void(0)';
                                                        $submenulink_pointer='menu-no-pointer';
                                                    } elseif ('node' == $parts[0]) {
                                                        $submenulink = url($submenu['link_url']);
                                                        $submenulink_pointer='';
                                                    } else {
                                                        $submenulink = $submenu['link_path'];
                                                        $submenulink_pointer='';
                                                    }
                                                  @endphp

                                                    <a href="{{ $submenulink }}"
                                                     class="dropdown-item  {{ @$submenulink_pointer }}"  {{-- class="dropdown-item" @if (!empty($submenu['megaMenu'])) dropdown-toggle @endif {{ isset($submenu['link_path']) ? '' : 'menu-no-pointer' }}"
                                                        @if (!empty($submenu['megaMenu'])) data-bs-toggle="dropdown" aria-expanded="false" @endif --}}>
                                                        <i class="fas fa-chevron-right me-2"></i>
                                                        {{ $submenu['link_title'] }}
                                                    </a>

                                                    @if (isset($submenu['megaMenu']) && count($submenu['megaMenu']) > 0)
                                                        <ul class="nestedMenu dropdown-submenu">
                                                            @foreach ($submenu['megaMenu'] as $nestedMenu)
                                                                <li>

                                                                    @php
                                                    $parts = explode('/', $nestedMenu['link_path']);

                                                    if (in_array($nestedMenu['link_path'], [config('app_constants.NOLINK'), config('app_constants.DROPLINK')])) {
                                                        $nestedMenulink = 'javascript:void(0)';
                                                        $nestedMenulink_pointer='menu-no-pointer';
                                                    } elseif ('node' == $parts[0]) {
                                                        $nestedMenulink = url($nestedMenu['link_url']);
                                                        $nestedMenulink_pointer='';
                                                    } else {
                                                        $nestedMenulink = $nestedMenu['link_path'];
                                                        $nestedMenulink_pointer='';
                                                    }
                                                  @endphp
                                                                    <a href="{{ $nestedMenulink }}"
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





                <div
                    class="col-lg-3 col-md-12 d-flex align-items-center justify-content-center justify-content-md-start">
                    <div
                        class="d-flex flex-wrap flex-md-nowrap align-items-center justify-content-center justify-content-md-start w-100">
                        <!-- Contact Info Section -->
                        <div class="contact-info d-flex flex-nowrap align-items-center gap-2">
                            <a href="tel:1-800-261-6861" class="contact-info-link">1-800-261-6861</a>
                            <span class="d-none d-md-inline">|</span>
                            <a href="mailto:info@procept.com" class="contact-info-link">info@procept.com</a>
                        </div>

                        <!-- Social Media Section -->
                        <div class="social-connect d-flex align-items-center gap-3 ms-2">
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
                </div>

            </div>
        </div>
    </nav>





    <!-- Message Bar -->
    <div class="message-bar d-flex justify-content-center align-items-center col-12">
        <marquee class="w-100">
            @foreach ($promotionalMessageBar as $promotional)
                @if (!empty($promotional->url))
                    <a href="{{ $promotional->url }}" class="message-link d-inline-block" target="_blank">
                        <p class="message-text d-inline">{{ $promotional->message }}</p>
                    </a>
                @else
                    <p class="d-inline">{{ $promotional->message }}</p>
                @endif
            @endforeach
        </marquee>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuItems = document.querySelectorAll('.nav-item.dropdown > a#mobile-content');

            mobileMenuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent the default link action

                    // Get the corresponding dropdown menu (the submenu)
                    const dropdownMenu = this.nextElementSibling;

                    // Toggle the visibility of the dropdown menu
                    if (dropdownMenu) {
                        dropdownMenu.classList.toggle('show');
                    }
                });
            });

            // Close dropdowns when clicking outside of them
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-item')) {
                    mobileMenuItems.forEach(item => {
                        const dropdownMenu = item.nextElementSibling;
                        if (dropdownMenu) {
                            dropdownMenu.classList.remove('show');
                        }
                    });
                }
            });
        });
    </script>
    <!-- JavaScript for Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
