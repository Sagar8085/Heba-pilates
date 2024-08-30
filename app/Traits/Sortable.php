<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Sortable
 *
 * @package App\Traits
 */
trait Sortable
{
    public function scopeSort(Builder $builder, $sortables)
    {
        return $sortables->apply($builder);
    }
}