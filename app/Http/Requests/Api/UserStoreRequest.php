<?php

namespace App\Http\Requests\Api;

use App\Mail\SendEmail;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return $this->bearerToken() === config('api.token');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'password' => Hash::make(Str::random(32)),
            'guest_status' => 'Created Externally',
        ]);
    }

    public function rules()
    {
        return [
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'phone_number' => [
                'required',
            ],
            'password' => [
                'required',
            ],
            'guest_status' => [
                'required',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->notify();

        parent::failedValidation($validator);
    }

    protected function failedAuthorization()
    {
        $this->notify('bearer');

        parent::failedAuthorization();
    }

    public function notify(string $reason = 'validation')
    {
        $reason = [
            'bearer' => 'The most likely reason for this is an incorrect bearer token',
            'validation' => 'The most likely reason for this is incorrect data in the request',
        ][$reason];

        $mail = new SendEmail(
            'hello@hebapilates.com',
            'emails.user.user-creation-unsuccessful',
            'External API User Creation Failed',
            compact('reason'),
            'hello@hebapilates.com'
        );

        Mail::to('hello@hebapilates.com')->send($mail);
    }
}
