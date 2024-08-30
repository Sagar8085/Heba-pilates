<?php

namespace App\Calculations;

use App\Contracts\Calculator;
use JetBrains\PhpStorm\Pure;

/**
 * Class PercentageDifference
 *
 * @package App\Sums;
 */
class PercentageDifference implements Calculator
{
    const MINUS = '-';
    const PLUS = '+';
    const POSITIVE = 'positive';
    const NEGATIVE = 'negative';

    protected int $original;

    protected int $new;

    function __construct(int $original, int $new)
    {
        $this->setOriginal($original);
        $this->setNew($new);
    }

    #[Pure] public function calculate(): string
    {
        $original = $this->getOriginal();

        return $this->getModifier()
            . ($original ? round((abs($this->getNew() - $original) / $original) * 100) : 0)
            . '%';
    }

    #[Pure] public function getModifier(): string
    {
        return $this->areNumbersTheSame() ? '' : ($this->isOriginalHigher() ? self::MINUS : self::PLUS);
    }

    #[Pure] public function getCalculationResult(): string
    {
        return $this->isOriginalHigher() ? self::NEGATIVE : self::POSITIVE;
    }

    public function getOriginal(): int
    {
        return $this->original;
    }

    public function setOriginal(int $original): void
    {
        $this->original = $original;
    }

    public function getNew(): int
    {
        return $this->new;
    }

    public function setNew(int $new): void
    {
        $this->new = $new;
    }

    #[Pure] protected function areNumbersTheSame(): bool
    {
        return $this->getOriginal() === $this->getNew();
    }

    #[Pure] protected function isOriginalHigher(): bool
    {
        return $this->getOriginal() > $this->getNew();
    }
}