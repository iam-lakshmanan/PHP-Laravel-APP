<?php

namespace App\DTO;

use App\Http\Requests\VerifySecretKeyRequest;

class VerifySecretKeyDTO
{
    public function __construct(
        public ?string $secret_key = null,
        public ?string $otp = null,
        public ?string $email = null
    ) {}

    public static function fromRequest(VerifySecretKeyRequest $request): self
    {
        return new self(
            secret_key: $request->has('secret_key') ? trim((string) $request->input('secret_key')) : null,
            otp: $request->has('otp') ? trim((string) $request->input('otp')) : null,
            email: $request->has('email') ? trim((string) $request->input('email')) : null
        );
    }
}
    