<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class CollectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('roundSum', fn (int $precision = 2) => round($this->sum(), $precision));
        Collection::macro('roundAll', fn (int $precision = 2) => $this->map(fn ($value) => round($value, 2)));
        Collection::macro('extract',
            fn (string $fieldToPluck = 'value') => $this->map('json_decode')->pluck($fieldToPluck));
        Collection::macro('removeTextualValues',
            fn (string|null $reject = 'null') => $this->reject(fn (string|null $range) => $range === $reject));
        Collection::macro('replaceTextNullWithNull',
            fn () => $this->map(fn ($item) => $item === 'null' ? null : $item));
    }
}
