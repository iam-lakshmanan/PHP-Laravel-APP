<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAppDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id' => 'nullable|string|max:255',
            'fcm_token' => 'nullable|string',
            'platform'  => 'required|in:android,ios,web',
        ];
    }
}
