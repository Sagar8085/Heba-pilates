<?php

namespace App\Http\Requests\Member;

use Illuminate\Validation\Rule;

class StoreCreditPackRequest extends CreditPackRequest
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
            'type' => [
                'required',
                Rule::in([
                    static::TYPE_FREE,
                    static::TYPE_PREPAID,
                    static::TYPE_CARD,
                ]),
            ],
        ];
    }
}
