<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExerciseVideoRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'duration' => 'required',
            'duration_per_rep' => 'required',
            'selected_sections' => 'required',
            'image' => 'required',
            'video' => 'required',
            'selected_focuses' => 'required',
            'selected_intensity' => 'required',
        ];
    }
}
