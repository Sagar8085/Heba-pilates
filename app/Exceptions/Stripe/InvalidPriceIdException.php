<?php

namespace App\Exceptions\Stripe;

use Exception;

class InvalidPriceIdException extends Exception
{
    protected $message = 'Invalid Stripe price id';
}
