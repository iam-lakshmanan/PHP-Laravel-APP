
                          {{-- {{ dd($menuBarDetails) }} --}}
                          @foreach ($menuBarDetails as $menu)
                          <li class="nav-item @if (!empty($menu['subMenu'])) dropdown @endif nav_space w-100">
                              @if (!empty($menu['link_path']))
                                  <!-- Desktop View -->
                                  <a href="{{ $menu['link_path'] }}"
                                      class="nav-link d-flex align-items-center menu-item d-none d-md-flex"
                                      id="desktop-content">
                                      {{ $menu['link_title'] }}
                                  </a>
                                  <!-- Mobile View -->
                                  <a href="javascript:void(0);"
                                      class="nav-link d-flex align-items-center menu-item d-flex d-md-none menu-no-pointer"
                                      id="mobile-content" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      {{ $menu['link_title'] }}
                                  </a>
                              @else
                                  <span class="nav-link d-flex align-items-center menu-item">
                                      {{ $menu['link_title'] }}
                                  </span>
                              @endif

                              <!-- Submenu Dropdown -->
                              @if (!empty($menu['subMenu']))
                                  <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                      <!-- Loop through Submenu items -->
                                      @foreach ($menu['subMenu'] as $submenu)
                                          <li class="dropdown-submenu">
                                              @if (!empty($submenu['link_path']))
                                                  <a href="{{ $submenu['link_path'] }}" class="dropdown-item menu-title">
                                                      {{ $submenu['link_title'] }}
                                                  </a>
                                              @else
                                                  <span class="dropdown-item menu-title">
                                                      {{ $submenu['link_title'] }}
                                                  </span>
                                              @endif

                                              <!-- Subdivider -->
                                              <hr class="dropdown-divider d-lg-none d-sm-block">
                                          </li>
                                      @endforeach
                                  </ul>
                              @endif
                          </li>
                      @endforeach
