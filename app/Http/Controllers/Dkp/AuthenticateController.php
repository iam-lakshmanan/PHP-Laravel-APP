<?php

namespace App\Http\Controllers\Dkp;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifySecretKeyRequest;
use App\Http\Requests\RegisterDetailsRequest;
use App\DTO\VerifySecretKeyDTO;
use App\DTO\RegisterDetailsDTO;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthenticateController extends Controller
{
    /**
     * @var AuthService
     */
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Show Authentication View (Step 1, Step 2, or Step 3)
     */
    public function index(Request $request)
    {
        $step = (int) $request->query('step', 1);

        // If trying to access Step 2 or Step 3, ensure a valid secret key is in session
        if (in_array($step, [2, 3])) {
            $secretKey = session('dkp_secret_key');

            if (empty($secretKey)) {
                return redirect()->to(url('/materials?step=1'))
                    ->with('error', 'Please enter your secret key first.');
            }

            $checkthekeyrevoked = $this->service->isKeyRevoked($secretKey);

            if ($checkthekeyrevoked == 'revoked') {
                return redirect()->to(url('/materials?step=1'))
                    ->with('error', 'Your secret key has been revoked.');
            }

            $keyexpired = $this->service->isKeyExpired($secretKey);

            if ($keyexpired == 'expired') {
                return redirect()->to(url('/materials?step=1'))
                    ->with('error', 'Your secret key has expired.');
            }

            // Verify that the secret key is valid in the database
            $keyDetails = $this->service->getKeyDetails($secretKey);

            if (empty($keyDetails['success'])) {
                session()->forget(['dkp_secret_key', 'dkp_key_id']);

                return redirect()->to(url('/materials?step=1'))
                    ->with('error', 'Invalid secret key. Please enter your secret key again.');
            }

            // Returning user (alreadylogin) must go to step 3; new user must go to step 2
            if (!empty($keyDetails['alreadylogin']) && $step === 2) {
                return redirect()->to(url('/materials?step=3'));
            }

            if (empty($keyDetails['alreadylogin']) && $step === 3) {
                return redirect()->to(url('/materials?step=2'));
            }
        }

        return view('dkp-adminpanel.auth.login', compact('step'));
    }

    /**
     * Handle Step 1: Verify Secret Key
     * Flow: Request -> DTO -> Controller -> Service -> Repository
     */
    public function verifySecretKey(VerifySecretKeyRequest $request)
    {
        $dto = VerifySecretKeyDTO::fromRequest($request);
        $result = $this->service->verifySecretKey($dto);

        if (empty($result['success'])) {
            return redirect()->back()
                ->with('error', $result['message'] ?? 'Invalid secret key. Please try again.');
        }

        $keyDetails = $this->service->getKeyDetails($dto->secret_key);

        if (!empty($keyDetails['alreadylogin'])) {
            return redirect()->to(url('/materials?step=3'));
        }

        return redirect()->to(url('/materials?step=2'));
    }

    /**
     * Handle already registered check or route redirect.
     */
    public function alreadylogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'secret_key' => ['required', 'string'],
            ]);

            session([
                'dkp_secret_key' => $request->input('secret_key'),
            ]);
        }

        $secretKey = session('dkp_secret_key');

        if (empty($secretKey)) {
            return redirect()->to(url('/materials?step=1'))
                ->with('error', 'Please enter your secret key.');
        }

        $keyDetails = $this->service->getKeyDetails($secretKey);

        if (empty($keyDetails['success'])) {
            session()->forget('dkp_secret_key');

            return redirect()->to(url('/materials?step=1'))
                ->with('error', 'Invalid secret key. Please try again.');
        }

        if (!empty($keyDetails['alreadylogin'])) {
            return redirect()->to(url('/materials?step=3'));
        }

        return redirect()->to(url('/materials?step=2'));
    }

    /**
     * Verify email for already registered user and redirect to dashboard.
     */
    public function verifyExistingEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        $secretKey = session('dkp_secret_key');

        if (empty($secretKey)) {
            return redirect()->to(url('/materials?step=1'))
                ->with('error', 'Session expired. Please enter your secret key again.');
        }

        $keyRecord = $this->service->findKey($secretKey);

        if (!$keyRecord) {
            return redirect()->to(url('/materials?step=1'))
                ->with('error', 'Invalid secret key.');
        }

        $existingUser = $this->service->getUserLoginByKeyId($keyRecord->key_id);

        if (!$existingUser || ($keyRecord->use_count ?? 0) == 0) {
            return redirect()->to(url('/materials?step=2'))
                ->with('error', 'No registration found for this key. Please complete your details.');
        }

        $submittedEmail = strtolower(trim($request->input('email')));
        $registeredEmail = strtolower(trim($existingUser->email));

        if ($submittedEmail !== $registeredEmail) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'email' => 'The email address does not match the registered user for this key.',
                ]);
        }

        // Email matches! Store user in session and authenticate
        session([
            'dkp_user' => [
                'first_name' => $existingUser->first_name,
                'last_name'  => $existingUser->last_name,
                'email'      => $existingUser->email,
                'client_id'  => $keyRecord->client_id,
            ],
            'dkp_authenticated' => true,
            'dkp_key_id'        => $keyRecord->key_id,
            'dkp_secret_key'    => $secretKey,
        ]);

        // Increment key usage
        $this->service->incrementKeyUse($keyRecord->key_id);

        return redirect()->route('materials.dashboard');
    }



    /**
     * Handle Step 2: Register User Details
     * Flow: Request -> DTO -> Controller -> Service -> Repository
     */
    public function registerDetails(RegisterDetailsRequest $request)
    {
        $secretKey = session('dkp_secret_key');

        if (empty($secretKey)) {
            return redirect()->to(url('/materials?step=1'))
                ->with('error', 'Session expired. Please enter your secret key again.');
        }

        $dto = RegisterDetailsDTO::fromRequest($request, $secretKey);

        $emailExists = $this->service->checkEmailExists($dto->email);

        if ($emailExists) {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'This email address is already registered. Please use a different email.',
            ]);
        }

        $registerDetailsComplete = $this->service->registerDetails($dto);

        // dd($registerDetailsComplete);

        if ($registerDetailsComplete) {
            // dd($registerDetailsComplete);
            $otpsentousermail = $this->service->sendOtpToUserEmail($dto->email);
        } else {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'Failed to register your details. Please try again.',
            ]);
        }

        if (!$otpsentousermail) {
            return redirect()->back()->withErrors(['email' => 'Failed to send OTP email. Please try again.']);
        }

        return redirect()->route('materials.verifyOtpForm')->with('email', $dto->email);
    }


    /**
     * Show OTP Verification View (Step 3)
     */
    public function showOtpVerificationForm()
    {
        $secretKey = session('dkp_secret_key');
        $email = session('dkp_user.email');

        if (empty($secretKey)) {
            return redirect()->to(url('/materials?step=1'))
                ->with('error', 'Please enter your secret key first.');
        }

        if (!$email) {
            return redirect()->to(url('/materials?step=2'))
                ->withErrors([
                    'email' => 'Email not found. Please complete your details first.',
                ]);
        }

        // If OTP is already verified OR user is already registered without an active OTP awaiting verification,
        // do not allow access to the OTP page - redirect directly to the dashboard.
        $alreadyRegistered = $this->service->checkEmailExists($email);
        $hasActiveOtp = session()->has('dkp_otp');

        if (session('dkp_otp_verified') === true || ($alreadyRegistered && !$hasActiveOtp)) {
            return redirect()->route('materials.dashboard');
        }

        // If no OTP was generated yet (user didn't submit Step 2), redirect to Step 2
        if (!$hasActiveOtp) {
            return redirect()->to(url('/materials?step=2'));
        }

        return response()
            ->view('dkp-adminpanel.auth.verify-otp', compact('email'))
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }


    /**
     * Resend OTP to the user's email.
     */
    public function resendOtp()
    {
        $email = session('dkp_user.email');

        if (!$email) {
            return redirect()->route('materials.index')
                ->withErrors([
                    'email' => 'Session expired. Please enter your details again.',
                ]);
        }

        $otpSent = $this->service->sendOtpToUserEmail($email);

        if (!$otpSent) {
            return redirect()->route('materials.verifyOtpForm')
                ->withErrors([
                    'otp' => 'Failed to resend OTP. Please try again.',
                ]);
        }

        return redirect()->route('materials.verifyOtpForm')
            ->with('success', 'A new OTP has been sent to your email.');
    }

    /**
     * Return to Step 2: Complete Your Details.
     */
    public function backToDetails()
    {
        if (!session()->has('dkp_secret_key') || !session()->has('dkp_user.email')) {
            return redirect()->to(url('/materials?step=1'))
                ->with('error', 'Session expired. Please enter your secret key again.');
        }

        return redirect()->to(url('/materials?step=2'));
    }

    /**
     * Handle Step 3: Verify OTP Key
     * Flow: Request -> DTO -> Controller -> Service -> Repository
     */
    public function verifyOtpKey(VerifySecretKeyRequest $request)
    {
        $dto = VerifySecretKeyDTO::fromRequest($request);
        $useremail = session('dkp_user.email');

        if (!$useremail) {
            return redirect()->route('materials.verifyOtpForm')
                ->withErrors([
                    'email' => 'Your session has expired. Please start again.',
                ]);
        }

        // Ensure the submitted email (if present) matches the email saved in the session.
        if ($dto->email && strcasecmp(trim($dto->email), trim($useremail)) !== 0) {
            return redirect()->route('materials.verifyOtpForm')
                ->withErrors([
                    'email' => 'The email address does not match your registration.',
                ]);
        }

        Log::info('Verifying OTP submission', [
            'email'         => $useremail,
            'submitted_otp' => $dto->otp,
            'session_otp'   => session('dkp_otp'),
        ]);

        $isValidOtp = $this->service->verifyOtp($useremail, $dto->otp);

        if (!$isValidOtp) {
            Log::warning('OTP verification failed', [
                'email'         => $useremail,
                'submitted_otp' => $dto->otp,
            ]);

            return redirect()->route('materials.verifyOtpForm')
                ->withErrors([
                    'otp' => 'Invalid or expired OTP. Please try again.',
                ]);
        }

        Log::info('OTP verified successfully', [
            'email' => $useremail,
        ]);

        session([
            'dkp_otp_verified'  => true,
            'dkp_authenticated' => true,
        ]);

        return redirect()->route('materials.dashboard');
    }

    /**
     * Logout user and clear DKP session.
     */
    public function logout()
    {
        session()->forget(['dkp_user', 'dkp_authenticated', 'dkp_key_id', 'dkp_secret_key', 'dkp_otp', 'dkp_otp_verified']);
        return redirect()->route('materials.index')->with('success', 'You have been logged out successfully.');
    }
}
