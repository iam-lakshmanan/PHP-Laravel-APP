<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifySecretKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('*verifyOtpKey*') || $this->has('otp')) {
            return [
                'otp'   => 'required|string|size:6',
                'email' => 'nullable|email|max:191',
            ];
        }

        return [
            'secret_key' => 'required|string|min:3|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'secret_key.required' => 'Please enter your secret key.',
            'otp.required'        => 'Please enter the 6-digit OTP code.',
            'otp.size'            => 'The OTP code must be 6 digits.',
        ];
    }
}
