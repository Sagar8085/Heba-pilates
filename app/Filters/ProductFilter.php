<?php

namespace App\Filters;

/**
 * Class ProductFilter
 *
 * @package App\Filters;
 */
class ProductFilter extends Base
{
    protected array $filters = [
        'search',
    ];

    public function search($value): void
    {
        $this->builder->where('name', 'LIKE', '%' . $value . '%');
    }
}