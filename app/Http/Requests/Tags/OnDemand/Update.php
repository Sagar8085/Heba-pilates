<?php

namespace App\Http\Requests\Tags\OnDemand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tags' => [
                'required',
                'array',
            ],
            'tags.*.id' => [
                'required',
                Rule::exists('tags', 'id'),
            ],
        ];
    }
}
