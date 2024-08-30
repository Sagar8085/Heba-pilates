<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Index
 *
 * @package App\Http\Requests\Users;
 */
class Index extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    protected function prepareForValidation()
    {
        collect($this->all())
            ->keys()
            ->filter(fn (string $key) => in_array($this->input($key), ['true', 'false']))
            ->each(function (string $key) {
                $this->merge([
                    $key => json_decode($this->input($key)),
                ]);
            });
    }
}