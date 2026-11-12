<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lang' => 'nullable|string'
        ];
    }

    protected function prepareForValidation()
    {
        // Support ?en format
        if (!$this->has('lang')) {
            $key = array_key_first($this->query());

            if ($key) {
                $this->merge([
                    'lang' => $key
                ]);
            }
        }
    }
}
