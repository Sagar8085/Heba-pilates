<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

abstract class CreditPackRequest extends FormRequest
{
    const TYPE_FREE = 'free';
    const TYPE_PREPAID = 'prepaid';
    const TYPE_CARD = 'card';

    public function prepaid(): bool
    {
        return $this->input('type') === self::TYPE_PREPAID;
    }

    public function event(): string
    {
        switch ($this->input('type')) {
            case static::TYPE_FREE:
                return 'Free Credit Pack Added';
            case static::TYPE_PREPAID:
                return auth()->user()->name . ' added a Prepaid Credit Pack';
            default:
                return 'Purchased Credit Pack via Admin Panel';
        }
    }
}
