<?php

namespace Tests\Traits;

/**
 * Trait HandlesGenders
 *
 * @paclage Tests\Traits
 */
trait HandlesGenders
{
    public function genders(): array
    {
        return [
            ['male', '{"slug":"male","name":"Male"}'],
            ['female', '{"slug":"female","name":"Female"}'],
            ['other', '{"slug":"other","name":"Other"}'],
        ];
    }
}