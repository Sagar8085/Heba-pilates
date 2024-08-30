<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

/**
 * Class HexadecimalColorCode
 *
 * @package App\Helpers;
 */
class HexadecimalColorCode
{
    /**
     * @param string $key
     * @return string
     */
    public static function get(string $key): string
    {
        return Arr::get(
            $colours = config('hexadecimal_colours'),
            $key . '.colour',
            collect($colours)->pluck('colour')->random()
        );
    }
}