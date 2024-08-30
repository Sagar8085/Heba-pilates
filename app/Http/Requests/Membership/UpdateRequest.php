<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Sentry;

class UpdateRequest extends FormRequest
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
            'expires' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'expires' => $this->prepareExpires(),
        ]);
    }

    protected function prepareExpires()
    {
        $expires = $this->input('expires');

        if (!is_null($expires)) {
            try {
                $expires = Carbon::parse($expires)->endOfDay();
            } catch (\Exception $e) {
                Sentry::captureException($e);
                $expires = null;
            }
        }

        return $expires;
    }
}
