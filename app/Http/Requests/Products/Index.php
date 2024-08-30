<?php

namespace App\Http\Requests\Products;

/**
 * Class Index
 *
 * @package App\Http\Requests\Products
 */
class Index extends Base
{
    public function rules(): array
    {
        return [];
    }

    protected function prepareForValidation(): void
    {
        //
    }
}
