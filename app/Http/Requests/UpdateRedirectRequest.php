<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRedirectRequest extends FormRequest
{
    public function authorize()
    {
        // Implemente a lógica de autorização conforme necessário.
        // Permitindo por padrão.
        return true;
    }

    public function rules()
    {
        return [
            'destination_url' => [
                'sometimes',
                'url',
                'active_url',
                'starts_with:https',
                function ($attribute, $value, $fail) {
                    if (strpos($value, url('/')) === 0) {
                        $fail('The '.$attribute.' cannot point to the application itself.');
                    }
                },
            ],
            'active' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'destination_url.url' => 'The destination URL must be a valid URL.',
            'destination_url.active_url' => 'The destination URL must be active and return a status code of 200.',
            'destination_url.starts_with' => 'The destination URL must start with https.',
            'destination_url.not_in' => 'The destination URL cannot point to the application itself.',
            'active.boolean' => 'The active field must be true or false.',
        ];
    }
}
