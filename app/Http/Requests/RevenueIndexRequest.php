<?php

namespace App\Http\Requests;

use App\Constants\Unit;
use App\Rules\ValidComparisonRange;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RevenueIndexRequest
 *
 * @package App\Http\Requests
 */
class RevenueIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function rules(): array
    {
        return [
            'compare_date_from' => [
                new ValidComparisonRange($this->only([
                    'report_date_from',
                    'report_date_to',
                    'compare_date_to',
                ])),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->filled('report_date_from')) {
            $this->merge([
                'report_date_from' => now()
                    ->subDays(config('dashboard.revenue.default_day_range'))
                    ->toDateString(),
            ]);
        }

        if (!$this->filled('report_date_to')) {
            $this->merge([
                'report_date_to' => now()->toDateString(),
            ]);
        }

        if (!$this->filled('chartUnit')) {
            $this->merge([
                'chartUnit' => Unit::DAY,
            ]);
        }
    }
}
