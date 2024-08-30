<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Base
 *
 * @package App\Http\Requests\Products
 */
abstract class Base extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('products', 'name')
                    ->ignore(optional($this->route('product'))->getKey()),
            ],
            'price' => [
                'integer',
                'required',
            ],
            'active' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('price') && is_numeric($this->input('price'))) {
            $this->merge([
                'price' => $this->input('price') * 100,
            ]);
        }
    }
}
