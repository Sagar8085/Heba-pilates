<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class Base
 *
 * @package App\Filters;
 */
class Base
{
    protected Request $request;
    protected Builder $builder;
    protected array $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            $filter = Str::of($filter)->camel()->__toString();
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    /**
     * @return array
     */
    private function getFilters(): array
    {
        return array_filter($this->request->only($this->filters), fn ($item) => $item !== null);
    }
}