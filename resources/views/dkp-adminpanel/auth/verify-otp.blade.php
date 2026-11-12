
@include('dkp-adminpanel.auth.layout')

<body class="min-h-screen bg-[#fcfcfd] flex items-center justify-center p-4 sm:py-6 sm:px-8">

    <main class="w-full max-w-[720px] bg-white rounded-[28px] secret-card
                 px-6 sm:px-12 md:px-16 py-8 sm:py-10
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
        <div class="flex justify-center items-center mb-6">
            <img src="{{ asset($logo) }}" alt="{{ $alt }}" class="w-[155px] h-auto object-contain">
        </div>

        {{-- Step Indicator --}}
        <p class="text-xs sm:text-[13px] text-[#6b7280] font-medium tracking-[0.1em] uppercase mb-2">
            STEP 3 OF 3
        </p>

        {{-- Heading --}}
        <h1 class="text-xl sm:text-[22px] font-bold text-[#111827] text-center mb-1.5">
            Verify Your Email
        </h1>

        <p class="text-sm sm:text-[15px] text-[#374151] text-center leading-relaxed">
            We’ve sent a 6-digit verification code to
        </p>

        <p class="text-base sm:text-[17px] font-bold text-[#111827] text-center mt-1 mb-5 break-all">
            {{ $email }}
        </p>

        {{-- OTP Form --}}
        <form id="otp-form" action="{{ route('materials.verifyOtpKey') }}" method="POST" class="w-full max-w-[490px]">

            @csrf

            @php
                // Only prefill from URL query if there are NO errors and NO flash messages (e.g. resend success)
                $urlOtp = '';
                if (!$errors->any() && !session('success') && !session('error')) {
                    $urlOtp = preg_replace('/\D/', '', (string) request()->query('otp', ''));
                }
                $urlDigits = !empty($urlOtp) ? str_split(substr($urlOtp, 0, 6)) : [];
            @endphp

            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="otp" id="otp" value="{{ $urlOtp }}">

            {{-- Six OTP Input Boxes --}}
            <div id="otp-boxes"
                 class="flex justify-center gap-2 sm:gap-3 mb-4"
                 role="group"
                 aria-label="6-digit email verification code">

                @for ($i = 0; $i < 6; $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="1"
                        autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                        aria-label="OTP digit {{ $i + 1 }}"
                        value="{{ $urlDigits[$i] ?? '' }}"
                        class="otp-digit w-10 h-11 sm:w-[48px] sm:h-[48px]
                               rounded-xl border border-[#dedede]
                               text-center text-xl font-semibold text-black
                               focus:border-[#034b82] focus:ring-2
                               focus:ring-[#034b82]/10 outline-none"
                        required>
                @endfor

            </div>

            {{-- OTP Error --}}
            @if ($errors->has('otp'))
                <p class="text-red-600 text-xs sm:text-sm text-center mb-3">
                    {{ $errors->first('otp') }}
                </p>
            @endif

            @if (session('error'))
                <p class="text-red-600 text-xs sm:text-sm text-center mb-3">
                    {{ session('error') }}
                </p>
            @endif

            @if (session('success'))
                <p class="text-green-600 text-xs sm:text-sm text-center mb-3">
                    {{ session('success') }}
                </p>
            @endif

            {{-- Resend OTP --}}
            <div class="text-center text-xs sm:text-sm mb-4">
                <span class="text-[#6b7280]">Didn’t receive the code?</span>

                <button
                    type="submit"
                    form="resend-otp-form"
                    class="text-[#0784ed] font-medium hover:underline ml-1">
                    Resend code
                </button>
            </div>

            {{-- Verify Button --}}
            <button type="submit"
                    class="access-button w-full h-[46px] rounded-xl
                           text-white text-base font-semibold
                           flex items-center justify-center gap-2.5 shadow-sm">
                <span>Verify &amp; Continue</span>

                <svg class="w-5 h-5" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24"
                     aria-hidden="true">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>

        {{-- Separate Resend Form --}}
        <form id="resend-otp-form" action="{{ route('materials.resend-otp') }}" method="POST">
            @csrf
        </form>

        {{-- Back Button --}}
        <a href="{{ route('materials.back-to-details') }}"
           class="mt-4 sm:mt-5 flex items-center gap-2 text-[#034b82]
                  text-sm sm:text-base font-semibold hover:underline">

            <svg class="w-5 h-5" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24"
                 aria-hidden="true">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 12H5m7 7-7-7 7-7"/>
            </svg>

            Back
        </a>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Strip ?otp= from the browser URL address bar immediately so that
            // page reloads or subsequent submissions don't reuse the stale URL parameter.
            if (window.location.search.includes('otp=')) {
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }

            const boxes = Array.from(
                document.querySelectorAll('.otp-digit')
            );

            const otpField = document.getElementById('otp');
            const form = document.getElementById('otp-form');
            const resendForm = document.getElementById('resend-otp-form');

            function updateOtp() {
                otpField.value = boxes.map(box => box.value).join('');
            }

            if (resendForm) {
                resendForm.addEventListener('submit', function () {
                    // Clear the boxes immediately when resending code so stale digits don't persist
                    boxes.forEach(box => {
                        box.value = '';
                    });
                    updateOtp();
                });
            }

            boxes.forEach((box, index) => {
                // When focusing or clicking, select any existing digit so typing replaces it directly
                box.addEventListener('focus', function () {
                    this.select();
                });

                box.addEventListener('click', function () {
                    this.select();
                });

                box.addEventListener('keydown', function (event) {
                    if (event.key >= '0' && event.key <= '9') {
                        // User typed a digit 0-9: directly replace box value and advance to next box
                        this.value = event.key;
                        updateOtp();

                        if (index < boxes.length - 1) {
                            boxes[index + 1].focus();
                            boxes[index + 1].select();
                        }
                        event.preventDefault();
                    } else if (event.key === 'Backspace') {
                        if (this.value) {
                            this.value = '';
                            updateOtp();
                        } else if (index > 0) {
                            boxes[index - 1].focus();
                            boxes[index - 1].value = '';
                            updateOtp();
                        }
                        event.preventDefault();
                    } else if (event.key === 'ArrowLeft' && index > 0) {
                        boxes[index - 1].focus();
                        boxes[index - 1].select();
                        event.preventDefault();
                    } else if (event.key === 'ArrowRight' && index < boxes.length - 1) {
                        boxes[index + 1].focus();
                        boxes[index + 1].select();
                        event.preventDefault();
                    }
                });

                // Input event fallback (handles mobile keyboards, autofill, etc.)
                box.addEventListener('input', function () {
                    const cleaned = this.value.replace(/\D/g, '');
                    this.value = cleaned ? cleaned.slice(-1) : '';
                    updateOtp();

                    if (this.value && index < boxes.length - 1) {
                        boxes[index + 1].focus();
                        boxes[index + 1].select();
                    }
                });

                box.addEventListener('paste', function (event) {
                    event.preventDefault();

                    const pasted = (
                        event.clipboardData.getData('text') || ''
                    ).replace(/\D/g, '').slice(0, 6);

                    pasted.split('').forEach((digit, i) => {
                        if (boxes[i]) {
                            boxes[i].value = digit;
                        }
                    });

                    updateOtp();

                    const targetIndex = Math.min(pasted.length, boxes.length - 1);
                    boxes[targetIndex]?.focus();
                    boxes[targetIndex]?.select();
                });
            });

            form.addEventListener('submit', function (event) {
                updateOtp();

                if (!/^\d{6}$/.test(otpField.value)) {
                    event.preventDefault();
                    const firstEmptyBox = boxes.find(box => !box.value);
                    if (firstEmptyBox) {
                        firstEmptyBox.focus();
                    }
                }
            });

            updateOtp();
            const firstEmptyBox = boxes.find(box => !box.value);
            if (firstEmptyBox) {
                firstEmptyBox.focus();
            } else {
                boxes[boxes.length - 1]?.focus();
            }
        });
    </script>
</body>
</html>
