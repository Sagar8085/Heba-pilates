<?php

namespace App\Contracts;

/**
 * Interface Calculator
 *
 * @package App\Contracts
 */
interface Calculator
{
    /**
     * @return string
     */
    public function calculate(): string;

    /**
     * @return string
     */
    public function getCalculationResult(): string;
}