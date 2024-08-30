<?php

namespace App\Http\Requests\Member;

use Illuminate\Validation\Rule;

class CallbackMembershipRequest extends MembershipRequest
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
            'tier' => [
                'required',
                'string',
                Rule::exists('subscription_tiers', 'slug'),
            ],
            'stripeSessionID' => [
                'required',
                'string',
            ],
        ];
    }
}
