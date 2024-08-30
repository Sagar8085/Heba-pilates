<?php

namespace App\Http\Requests\Tags\OnDemand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Store extends FormRequest
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
            'tag' => [
                'required',
                'string',
                'max:200',
            ],
            'ondemand_id' => [
                'required',
                Rule::exists('on_demand_videos', 'id'),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->input('tag'),
            'slug' => Str::slug($this->input('tag')),
            'category' => 'ondemand',
        ]);
    }
}
