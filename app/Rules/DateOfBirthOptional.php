<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class DateOfBirthOptional implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /**
         * A couple of validations are happening here.
         * 1. Check all fields have a value.
         * 2. Check the month is between 1 and 12.
         * 3. Check the day is between 1 and 31.
         */
        if ((trim(request('dob_year')) !== '' || trim(request('dob_month')) !== '' || trim(request('dob_day')) !== '')) {

            if ((trim(request('dob_year')) !== '' && trim(request('dob_month')) !== '' && trim(request('dob_day')) !== '')) {

                if (
                    (request('dob_month') < 1 || request('dob_month') > 12) ||
                    (request('dob_day') < 1 || request('dob_day') > 31)
                ) {
                    $this->custom_message = 'Invalid Date of Birth';
                    return false;
                }

                /**
                 * Now lets create a Carbon date of birth from the inputs.
                 */
                $dateOfBirth = Carbon::parse(request('dob_year') . '-' . request('dob_month') . '-' . request('dob_day'));

                /**
                 * Check the user is 13 or older.
                 */
                if ($dateOfBirth->age < 13) {
                    $this->custom_message = 'Sorry you must be at least 13 to register with our gym.';
                    return false;
                }

            } else {
                $this->custom_message = 'Enter all Date of Birth Fields.';
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->custom_message;
    }
}