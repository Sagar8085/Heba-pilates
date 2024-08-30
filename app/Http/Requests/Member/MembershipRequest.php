<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

abstract class MembershipRequest extends FormRequest
{
    const MEMBERSHIP_TYPE_FREE = 'free';
    const MEMBERSHIP_TYPE_PAID = 'already-paid';
    const MEMBERSHIP_TYPE_BACS = 'bacs';

    public function hasAlreadyPaid(): bool
    {
        return $this->input('type') === self::MEMBERSHIP_TYPE_PAID;
    }

    public function event(): string
    {
        switch ($this->input('type')) {
            case static::MEMBERSHIP_TYPE_FREE:
                return 'Free Membership Added via Admin Panel';
            case static::MEMBERSHIP_TYPE_PAID:
                return 'Already-Paid Membership added via Admin Panel';
            default:
                return 'Purchased Membership via Admin Panel';
        }
    }
}
