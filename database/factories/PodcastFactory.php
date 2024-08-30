<?php

namespace Database\Factories;

use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;

class PodcastFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Podcast::class;

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

        $prices = [0, 199, 299, 499, 999];

        $randomImage = array_rand($images);
        $randomPrice = array_rand($prices);

        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text($maxNbChars = 200),
            'audio_path' => 'https://volution-transcoded-videos.s3-eu-west-1.amazonaws.com/on-demand/raw-videos/3vSJ2cLRtkGl6xSwcvAKJVn0VxsB1PbTu2IclTIk.mp4',
            'image_path' => $images[$randomImage],
            'duration' => 1200,
            'created_by' => 1,
            'price' => $prices[$randomPrice]
        ];
    }
}
