<?php

namespace App\DTO;

use App\Http\Requests\RegisterDetailsRequest;

class RegisterDetailsDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $secret_key = null,
        public ?string $otp = null // Optional OTP field
    ) {}

    public static function fromRequest(RegisterDetailsRequest $request, ?string $secret_key = null): self
    {
        return new self(
            first_name: trim($request->input('first_name')),
            last_name:  trim($request->input('last_name')),
            email:      trim($request->input('email')),
            secret_key: $secret_key,
            otp: trim($request->input('otp')) // Include OTP in the DTO
        );
    }
}
