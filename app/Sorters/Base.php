<?php

namespace App\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class Base
 *
 * @package App\Sorters;
 */
abstract class Base
{
    protected Request $request;
    protected Builder $builder;
    protected array $sortables = [];
    protected string $sortFieldKey = 'sortField';
    protected string $sortDirectionFieldKey = 'sortDirection';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder
    {
        $this->setBuilder($builder);

        if ($this->doesntNeedSorting()) {
            return $this->getBuilder();
        }

        $sort = Str::of($this->getValue())->camel()->__toString();

        if (method_exists($this, $sort)) {
            $this->$sort($this->getSortDirectionValue());
        }

        return $this->getBuilder();
    }

    public function getSortFieldKey(): string
    {
        return $this->sortFieldKey;
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    public function getSortDirectionValue(): string
    {
        return $this->request->input($this->sortDirectionFieldKey);
    }

    private function doesntNeedSorting(): bool
    {
        return !$this->request->filled($this->getSortFieldKey());
    }

    private function getValue(): string
    {
        return $this->request->input($this->getSortFieldKey());
    }
}