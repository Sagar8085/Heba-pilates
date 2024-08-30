<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $images = [
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1500&q=80',
            'https://images.unsplash.com/photo-1594737626072-90dc274bc2bd?ixid=MXwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1100&q=80',
            'https://images.unsplash.com/photo-1603455778956-d71832eafa4e?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1651&q=80',
            'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=934&q=80',
            'https://images.unsplash.com/photo-1602192509154-0b900ee1f851?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1650&q=80',
            'https://images.unsplash.com/photo-1466637574441-749b8f19452f?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1100&q=80',
            'https://images.unsplash.com/photo-1562419988-0dbd7b2fa545?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1567&q=80',
            'https://images.unsplash.com/photo-1571689230986-c2dcb5f4c5f7?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=1650&q=80'
        ];

        $name = $this->faker->company;
        $randomImage = array_rand($images);

        return [
            'title' => $name,
            'slug' => Helper::generateSlug(Blog::class, $name),
            'content' => $this->faker->text($maxNbChars = 2500),
            'image_path' => $images[$randomImage],
            'created_by' => 1
        ];
    }
}
