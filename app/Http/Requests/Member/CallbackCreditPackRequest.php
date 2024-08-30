<?php

namespace App\Http\Requests\Member;

use Illuminate\Validation\Rule;

class CallbackCreditPackRequest extends CreditPackRequest
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
            'creditPackId' => [
                'required',
                Rule::exists('credit_packs', 'id'),
            ],
            'stripeSessionID' => [
                'required',
                'string',
            ],
        ];
    }
}
