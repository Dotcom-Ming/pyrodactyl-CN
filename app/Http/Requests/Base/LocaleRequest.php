<?php

namespace Pterodactyl\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;

class LocaleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', 'regex:/^[a-z]{2}(_[A-Z]{2})?$/'],
            'namespace' => ['required', 'string', 'regex:/^[a-z_\/]{1,191}$/'],
        ];
    }
}
