@php
    $otp = (string) ($otp ?? '123456');
    $email = $email ?? 'user@example.com';
    $appName = $appName ?? config('app.name', 'Procept');
    $headerText = $headerText ?? "Let's log you in";
    $bodyText = $bodyText ?? "Use this code to sign in to {$appName}.";
    $infoText = $infoText ?? "This code will securely log you in using";
    $footerText = $footerText ?? "If you didn't request this email, you can safely ignore it.";
    $buttonText = $buttonText ?? "Copy OTP";

    if (empty($verifyUrl)) {
        try {
            $verifyUrl = route('materials.verifyOtpForm');
        } catch (\Throwable $e) {
            $verifyUrl = url('/materials/verify-otp-form');
        }
    }

    if (empty($base64logo)) {
        $host = request()->getHost() ?? '';
        $logoFile = str_contains($host, 'barnesconti.com') ? 'img/barnesconti-logo.png' : 'img/procept-logo.png';
        $fullPath = public_path($logoFile);
        if (file_exists($fullPath)) {
            $mime = mime_content_type($fullPath) ?: 'image/png';
            $base64logo = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
        } else {
            $base64logo = asset($logoFile);
        }
    }

    $digits = str_split($otp);
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>{{ $headerText }}</title>
    <style type="text/css">
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                padding: 16px 12px !important;
            }
            .card-wrapper {
                padding: 32px 20px !important;
                border-radius: 16px !important;
            }
            .digit-box {
                width: 38px !important;
                height: 48px !important;
                line-height: 48px !important;
                font-size: 22px !important;
                margin: 0 2px !important;
            }
            .header-title {
                font-size: 22px !important;
            }
            .body-text {
                font-size: 14px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="background-color: #f3f4f6; padding: 48px 16px 56px 16px;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
            <tr>
                <td align="center">
                    <!-- Main Card -->
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" class="email-container" width="520" style="max-width: 520px; width: 100%; margin: 0 auto;">
                        <tr>
                            <td class="card-wrapper" style="background-color: #ffffff; border-radius: 20px; padding: 44px 40px; border: 1px solid #e5e7eb; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); text-align: center;">

                                @php
                                    $logoUrl = '';

                                    if (stripos($appName, 'Barnes') !== false || stripos($appName, 'Conti') !== false) {
                                        $logoUrl = 'https://procept.com/img/barnesconti-logo.png';
                                    } else {
                                        $logoUrl = 'https://procept.com/img/procept-logo.png';
                                    }
                                @endphp

                                <!-- Centered Brand Logo -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="margin: 0 auto 28px auto;">
                                    <tr>
                                        <td align="center">
                                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" width="160"
                                                style="display:block; max-width:160px;
                        max-height:48px; width:auto;
                        height:auto; border:0; outline:none;
                        margin:0 auto;">
                                        </td>
                                    </tr>
                                </table>

                                <!-- headerText: Headline -->
                                <h1 class="header-title" style="margin: 0 0 10px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 25px; font-weight: 700; color: #111827; line-height: 1.3; text-align: center; letter-spacing: -0.3px;">
                                    {{ $headerText }}
                                </h1>

                                <!-- bodyText: Instructional Text (without time limit) -->
                                <p class="body-text" style="margin: 0 0 32px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px; color: #4b5563; text-align: center;">
                                    {{ $bodyText }}
                                </p>

                                <!-- OTP Code Box: Digits Display (Click to Copy) -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 0 auto 30px auto; cursor: pointer;" title="Click to copy OTP" onclick="if(navigator.clipboard){navigator.clipboard.writeText('{{ $otp }}');}const label=document.getElementById('copyBtnText');if(label){label.innerText='Copied!';setTimeout(function(){label.innerText='Copy OTP';},2500);}">
                                    <tr>
                                        @foreach($digits as $digit)
                                            <td align="center" valign="middle" style="padding: 0 5px;">
                                                <div class="digit-box" style="width: 48px; height: 56px; line-height: 56px; text-align: center; font-size: 26px; font-weight: 700; color: #111827; background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace; box-sizing: border-box;">
                                                    {{ $digit }}
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                </table>

                                <!-- Action Button: Copy OTP -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 0 auto 28px auto;">
                                    <tr>
                                        <td align="center" style="border-radius: 10px; background-color: #034b82;">
                                            <a href="{{ $verifyUrl }}?otp={{ $otp }}"
                                               target="_blank"
                                               class="copy-otp-btn"
                                               title="Copy OTP to clipboard"
                                               onclick="if(navigator.clipboard){navigator.clipboard.writeText('{{ $otp }}');}const label=document.getElementById('copyBtnText');if(label){label.innerText='Copied!';setTimeout(function(){label.innerText='Copy OTP';},2500);}return false;"
                                               style="display: inline-block; padding: 13px 36px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 10px; background-color: #034b82; border: 1px solid #034b82; text-align: center; cursor: pointer;">
                                                <span style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                                    </svg>
                                                    <span id="copyBtnText" style="vertical-align: middle;">{{ $buttonText }}</span>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <!-- infoText: Security & Email Info -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 0 auto 24px auto;">
                                    <tr>
                                        <td align="center" style="border-top: 1px solid #f1f5f9; padding-top: 24px;">
                                            <p style="margin: 0 0 4px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #6b7280; text-align: center;">
                                                {{ $infoText }}
                                            </p>
                                            <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #034b82; text-align: center;">
                                                <a href="mailto:{{ $email }}" style="color: #034b82; text-decoration: none;">{{ $email }}</a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <!-- footerText: Request disclaimer -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td align="center">
                                            <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 18px; color: #9ca3af; text-align: center;">
                                                {{ $footerText }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                    <!-- /Main Card -->
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
