<?php

namespace App\DTO;

class UserAppDeviceDTO
{
    public function __construct(
        public ?string $device_id,
        public ?string $fcm_token,
        public string $platform
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            $request->filled('device_id') ? $request->device_id : null,
            $request->fcm_token,
            $request->platform
        );
    }
}
