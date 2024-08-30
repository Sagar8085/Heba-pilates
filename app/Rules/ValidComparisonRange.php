<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use JetBrains\PhpStorm\Pure;

class ValidComparisonRange implements Rule
{
    /**
     * @var Carbon|null
     */
    private ?Carbon $report_date_from = null;

    /**
     * @var Carbon|null
     */
    private ?Carbon $report_date_to = null;

    /**
     * @var Carbon|null
     */
    private ?Carbon $compare_date_to = null;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->setReportDateFrom(
            $this->convertDateIfPresent($data, 'report_date_from')
        );

        $this->setReportDateTo(
            $this->convertDateIfPresent($data, 'report_date_to')
        );

        $this->setCompareDateTo(
            $this->convertDateIfPresent($data, 'compare_date_to')
        );

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
        return !$value || $this->canCompare() && $this->getComparisonRange($value) === $this->getReportRange();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.invalid_comparison_range');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return Carbon|null
     */
    public function getCompareDateTo(): Carbon|null
    {
        return $this->compare_date_to;
    }

    /**
     * @return Carbon|null
     */
    public function getReportDateFrom(): Carbon|null
    {
        return $this->report_date_from;
    }

    /**
     * @return Carbon|null
     */
    public function getReportDateTo(): Carbon|null
    {
        return $this->report_date_to;
    }

    /**
     * @param Carbon|null $report_date_from
     */
    public function setReportDateFrom(?Carbon $report_date_from): void
    {
        $this->report_date_from = $report_date_from;
    }

    /**
     * @param Carbon|null $report_date_to
     */
    public function setReportDateTo(?Carbon $report_date_to): void
    {
        $this->report_date_to = $report_date_to;
    }

    /**
     * @param Carbon|null $compare_date_to
     */
    public function setCompareDateTo(?Carbon $compare_date_to): void
    {
        $this->compare_date_to = $compare_date_to;
    }

    /**
     * @return bool
     */
    #[Pure] private function canCompare(): bool
    {
        return !!$this->getCompareDateTo()
            && !!$this->getReportDateFrom()
            && !!$this->getReportDateTo();
    }

    /**
     * @param array $data
     * @param string $key
     * @return Carbon|null
     */
    private function convertDateIfPresent(array $data, string $key): ?Carbon
    {
        return Arr::has($data, $key) ? Carbon::parse(Arr::get($data, $key)) : null;
    }

    /**
     * @param string $date
     * @return int
     */
    private function getComparisonRange(string $date): int
    {
        return $this->getCompareDateTo()->diffInDays(Carbon::parse($date));
    }

    /**
     * @return int
     */
    private function getReportRange(): int
    {
        return $this->getReportDateFrom()->diffInDays($this->getReportDateTo());
    }
}
