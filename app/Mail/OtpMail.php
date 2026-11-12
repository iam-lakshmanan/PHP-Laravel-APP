<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $email;
    public $verifyUrl;
    public $appName;
    public $base64logo;
    public $headerText;
    public $bodyText;
    public $infoText;
    public $footerText;
    public $buttonText;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $otp
     */
    public function __construct(string $email, string $otp)
    {
        $this->email = $email;
        $this->otp = (string) $otp;

        // Dynamic branding based on host
        $host = request()->getHost() ?? '';
        if (str_contains($host, 'barnesconti.com')) {
            $this->appName = 'Barnes & Conti';
            $logoFile = 'img/barnesconti-logo.png';
        } else {
            $this->appName = config('app.name', 'Procept');
            $logoFile = 'img/procept-logo.png';
        }

        // Generate verification URL
        try {
            $this->verifyUrl = route('materials.verifyOtpForm');
        } catch (\Throwable $e) {
            $this->verifyUrl = url('/materials/verify-otp-form');
        }

        // Base64 encode logo for universal email client support
        $logoPath = public_path($logoFile);
        if (file_exists($logoPath)) {
            $mimeType = mime_content_type($logoPath) ?: 'image/png';
            $this->base64logo = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $this->base64logo = null;
        }

        // Content matching template specifications
        $this->headerText = "Let's log you in";
        $this->bodyText   = "Use this code to sign in to {$this->appName}.";
        $this->infoText   = "This code will securely log you in using";
        $this->footerText = "If you didn't request this email, you can safely ignore it.";
        $this->buttonText = "Copy OTP";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your One-Time Password (OTP)')
                    ->view('emails.otp');
    }
}
