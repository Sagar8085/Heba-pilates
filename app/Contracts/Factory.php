<?php

namespace App\Contracts;

/**
 * Interface Factory
 */
interface Factory
{
    public function get(string $value);
}