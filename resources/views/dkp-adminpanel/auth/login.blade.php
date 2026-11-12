@include('dkp-adminpanel.auth.layout')



<body class="min-h-screen bg-[#fcfcfd] flex items-center justify-center p-4 sm:p-8">

    <main
        class="w-full max-w-[720px] bg-white
             rounded-[28px] secret-card
             px-8 sm:px-16
             pt-12 pb-12
             flex flex-col items-center">

        @php
            $host = request()->getHost();

            if (str_contains($host, 'barnesconti.com')) {
                $logo = 'img/barnesconti-logo.png';
                $alt = 'Barnes & Conti';
            } else {
                $logo = 'img/procept-logo.png';
                $alt = 'Procept Associates Ltd.';
            }
        @endphp

        {{-- Dynamic Logo --}}
        <div class="flex justify-center items-center mb-8">
            <img src="{{ asset($logo) }}" alt="{{ $alt }}" class="w-[155px] h-auto object-contain">
        </div>

        {{-- ==================== STEP 1: ENTER SECRET KEY ==================== --}}
        <div id="step-1-container" class="w-full flex flex-col items-center {{ ($step ?? 1) == 1 ? '' : 'hidden' }}">
            {{-- Step Indicator --}}
            <p class="text-xs sm:text-[13px] text-[#6b7280] font-medium tracking-[0.1em] uppercase mb-2">
                STEP 1 OF 3
            </p>

            {{-- Heading --}}
            <h1 class="text-xl sm:text-[22px] font-bold text-[#111827] text-center mb-1.5">
                Enter Secret Key
            </h1>

            {{-- Description --}}
            <p class="text-sm sm:text-[15px] text-[#374151] text-center mb-8">
                Enter your secret key to access the platform
            </p>

            {{-- Secret Key Form --}}
            <form id="step-1-form" action="{{ url('/secret-key/verify') }}" method="POST" class="w-full max-w-[490px]">
                @csrf

                {{-- Secret Key Input --}}
                <div class="relative w-full mb-4">
                    <span
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9ca3af] pointer-events-none flex items-center">
                        <svg class="w-5 h-5 text-[#9ca3af]" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                    </span>

                    <input type="password" id="secret_key" name="secret_key" placeholder="Enter your secret key"
                        autocomplete="off" required
                        class="secret-input w-full h-[48px]
                               border border-[#e2e8f0] rounded-xl
                               pl-12 pr-12 text-sm sm:text-base text-[#1e293b]
                               placeholder:text-[#9ca3af]
                               focus:border-[#034b82]
                               focus:ring-2 focus:ring-[#034b82]/10 transition-all">

                    {{-- Show / Hide Password --}}
                    <button type="button" id="toggleSecretKey" aria-label="Show secret key"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9ca3af] hover:text-[#034b82] transition-colors focus:outline-none flex items-center">
                        <svg id="eyeIconSvg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </button>
                </div>

                {{-- Validation Error --}}
                @if ($errors->has('secret_key'))
                    <p class="text-red-600 text-xs sm:text-sm mb-3">
                        {{ $errors->first('secret_key') }}
                    </p>
                @endif

                {{-- General Error Message --}}
                @if (session('error'))
                    <p class="text-red-600 text-xs sm:text-sm mb-3">
                        {{ session('error') }}
                    </p>
                @endif

                {{-- Access Button --}}
                <button type="submit"
                    class="access-button w-full h-[46px] rounded-lg
                           text-white text-base font-medium
                           flex items-center justify-center gap-2 shadow-sm">
                    <span>Access</span>
                    <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                        </path>
                    </svg>
                </button>
            </form>

            {{-- Secure Access Divider --}}
            <div class="w-full max-w-[400px] flex items-center justify-center gap-3 mt-8 mb-4">
                <div class="flex-1 h-px bg-[#d1d5db]"></div>
                <span class="text-xs sm:text-sm font-normal text-[#374151] whitespace-nowrap">
                    Secure Access
                </span>
                <div class="flex-1 h-px bg-[#d1d5db]"></div>
            </div>

            {{-- Security Shield Outline Icon --}}
            <div class="text-[#111827] mb-2 flex items-center justify-center">
                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
            </div>

            {{-- Restricted Access Message --}}
            <p class="text-xs sm:text-[13px] text-[#4b5563] text-center leading-relaxed">
                This area is restricted. please use your authorized secret key.
            </p>
        </div>


        {{-- ==================== STEP 2: COMPLETE YOUR DETAILS ==================== --}}
        <div id="step-2-container"
            class="w-full flex flex-col items-center {{ ($step ?? 1) == 2 && session()->has('dkp_secret_key') ? '' : 'hidden' }}">
            {{-- Step Indicator --}}
            <p class="text-xs sm:text-[13px] text-[#6b7280] font-medium tracking-[0.1em] uppercase mb-2">
                STEP 2 OF 3
            </p>

            {{-- Heading --}}
            <h1 class="text-xl sm:text-[22px] font-bold text-[#111827] text-center mb-1.5">
                Complete Your Details
            </h1>

            {{-- Description --}}
            <p class="text-sm sm:text-[15px] text-[#374151] text-center mb-8">
                Please provide your information to continue
            </p>

            {{-- Complete Details Form --}}
            <form id="step-2-form" action="{{ url('/register/details') }}" method="POST" class="w-full max-w-[490px]">
                @csrf

                {{-- Row for First Name and Last Name --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    {{-- First Name --}}
                    <div>
                        <div class="relative w-full">
                            <span
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9ca3af] pointer-events-none flex items-center">
                                <svg class="w-5 h-5 text-[#9ca3af]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="text" id="first_name" name="first_name" placeholder="First Name" required
                                class="secret-input w-full h-[46px]
                                       border border-[#e2e8f0] rounded-xl
                                       pl-11 pr-3 text-sm text-[#1e293b]
                                       placeholder:text-[#9ca3af]
                                       focus:border-[#034b82]
                                       focus:ring-2 focus:ring-[#034b82]/10 transition-all">
                        </div>
                        <p class="text-[12px] text-[#9ca3af] mt-1 pl-1">e.g. John</p>
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <div class="relative w-full">
                            <span
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9ca3af] pointer-events-none flex items-center">
                                <svg class="w-5 h-5 text-[#9ca3af]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required
                                class="secret-input w-full h-[46px]
                                       border border-[#e2e8f0] rounded-xl
                                       pl-11 pr-3 text-sm text-[#1e293b]
                                       placeholder:text-[#9ca3af]
                                       focus:border-[#034b82]
                                       focus:ring-2 focus:ring-[#034b82]/10 transition-all">
                        </div>
                        <p class="text-[12px] text-[#9ca3af] mt-1 pl-1">e.g. Doe</p>
                    </div>
                </div>

                {{-- Email Address --}}
                <div class="mb-6">
                    <div class="relative w-full">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9ca3af] pointer-events-none flex items-center">
                            <svg class="w-5 h-5 text-[#9ca3af]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" placeholder="Email Address" required
                            class="secret-input w-full h-[46px]
                                   border border-[#e2e8f0] rounded-xl
                                   pl-11 pr-3 text-sm text-[#1e293b]
                                   placeholder:text-[#9ca3af]
                                   focus:border-[#034b82]
                                   focus:ring-2 focus:ring-[#034b82]/10 transition-all">
                    </div>
                    <p class="text-[12px] text-[#9ca3af] mt-1 pl-1">e.g. john.doe@company.com</p>
                </div>

                {{-- Continue Button --}}
                <button type="submit"
                    class="access-button w-full h-[46px] rounded-lg
                           text-white text-base font-medium
                           flex items-center justify-center gap-2 shadow-sm">
                    <span>Continue</span>
                    <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
        </div>



        {{-- ==================== STEP 3: ALREADY REGISTERED USER VIA LOGIN ==================== --}}
        <div id="step-3-container"
            class="w-full flex flex-col items-center {{ ($step ?? 1) == 3 && session()->has('dkp_secret_key') ? '' : 'hidden' }}">
            {{-- Step Indicator --}}
            <p class="text-xs sm:text-[13px] text-[#6b7280] font-medium
              tracking-[0.1em] uppercase mb-5">
                STEP 2 OF 2
            </p>

            {{-- Heading --}}
            <h1 class="text-xl sm:text-[24px] font-bold text-black
               text-center mb-2">
                Enter Email Address
            </h1>

            {{-- Description --}}
            <p class="text-sm sm:text-[16px] text-black text-center mb-8">
                Enter your registered email address to access the platform
            </p>

            {{-- Email Form --}}
            <form id="step-3-form" action="{{ route('materials.verifyExistingEmail') }}" method="POST"
                class="w-full max-w-[546px]">
                @csrf

                {{-- Email Input --}}
                <div class="relative w-full mb-3">
                    <span
                        class="absolute left-5 top-1/2 -translate-y-1/2
                       text-[#b5b5b5] pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8
                           M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                           a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>

                    <input type="email" id="existing_email" name="email" value="{{ old('email') }}"
                        placeholder="Email Address" autocomplete="email" required
                        class="secret-input w-full h-[52px]
                       border border-[#e2e2e2] rounded-xl
                       pl-14 pr-4 text-base text-[#1e293b]
                       placeholder:text-[#808080]
                       focus:border-[#034b82]
                       focus:ring-2 focus:ring-[#034b82]/10
                       transition-all" />
                </div>

                {{-- Email Example --}}
                <p class="text-sm text-[#808080] mb-3">
                    e.g. john.doe@company.com
                </p>

                {{-- Validation Errors --}}
                @if ($errors->has('email'))
                    <p class="text-red-600 text-sm mb-3">
                        {{ $errors->first('email') }}
                    </p>
                @endif

                {{-- General Error --}}
                @if (session('error'))
                    <p class="text-red-600 text-sm mb-3">
                        {{ session('error') }}
                    </p>
                @endif

                {{-- Continue Button --}}
                <button type="submit"
                    class="access-button w-full h-[52px] rounded-xl
                   text-white text-base sm:text-[20px] font-semibold
                   flex items-center justify-center gap-3 shadow-sm">
                    <span>Continue</span>

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H3" />
                    </svg>
                </button>
            </form>
        </div>


    </main>

    {{-- Show / Hide Secret Key & Step Navigation Helper --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('secret_key');
            const toggle = document.getElementById('toggleSecretKey');

            if (toggle && input) {
                const eyeOpenSvg =
                    `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
                const eyeSlashSvg =
                    `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>`;

                toggle.addEventListener('click', function() {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    toggle.innerHTML = isPassword ? eyeSlashSvg : eyeOpenSvg;
                    toggle.setAttribute('aria-label', isPassword ? 'Hide secret key' : 'Show secret key');
                });
            }

            // Client-side helper: support query param ?step=2 or ?step=3 to view the correct step immediately
            const hasSecretKey = {{ session()->has('dkp_secret_key') ? 'true' : 'false' }};
            const urlParams = new URLSearchParams(window.location.search);
            const currentStep = urlParams.get('step');
            const step1 = document.getElementById('step-1-container');
            const step2 = document.getElementById('step-2-container');
            const step3 = document.getElementById('step-3-container');

            if (!hasSecretKey) {
                if (step1) step1.classList.remove('hidden');
                if (step2) step2.classList.add('hidden');
                if (step3) step3.classList.add('hidden');
            } else if (currentStep === '2') {
                if (step1) step1.classList.add('hidden');
                if (step3) step3.classList.add('hidden');
                if (step2) step2.classList.remove('hidden');
            } else if (currentStep === '3') {
                if (step1) step1.classList.add('hidden');
                if (step2) step2.classList.add('hidden');
                if (step3) step3.classList.remove('hidden');
            }
        });
    </script>
</body>
