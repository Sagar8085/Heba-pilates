<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Filterable
 *
 * @package App\Traits
 */
trait Filterable
{
    public function scopeFilter(Builder $builder, $filters)
    {
        return $filters->apply($builder);
    }
}