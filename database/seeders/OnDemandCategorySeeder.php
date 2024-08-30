<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OnDemandCategory;

class OnDemandCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OnDemandCategory::create([
            'name' => 'Yoga',
            'slug' => 'yoga',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1500&q=80'
        ]);

        OnDemandCategory::create([
            'name' => 'Muscle Training',
            'slug' => 'muscle-training',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1594737626072-90dc274bc2bd?ixid=MXwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1100&q=80'
        ]);

        OnDemandCategory::create([
            'name' => 'Body Building',
            'slug' => 'body-building',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=934&q=80'
        ]);

        OnDemandCategory::create([
            'name' => 'Cardio',
            'slug' => 'cardio',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1603455778956-d71832eafa4e?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1651&q=80'
        ]);

        OnDemandCategory::create([
            'name' => 'Peace and Mind',
            'slug' => 'peace-and-mind',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1602192509154-0b900ee1f851?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1650&q=80'
        ]);

        OnDemandCategory::create([
            'name' => 'Healthy Lifestyle',
            'slug' => 'healthy-lifestyle',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1466637574441-749b8f19452f?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1100&q=80'
        ]);

        OnDemandCategory::create([
            'name' => 'HITT',
            'slug' => 'hitt',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1562419988-0dbd7b2fa545?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1567&q=80'
        ]);

        OnDemandCategory::create([
            'name' => 'Double Up',
            'slug' => 'doubleup',
            'description' => 'Et enim odit quia autem ut tempore est. Molestias quidem molestiae et molestiae voluptates exercitationem similique. Suscipit fuga rerum maxime voluptatum ratione a aperiam.',
            'created_by' => 1,
            'image_path' => 'https://images.unsplash.com/photo-1571689230986-c2dcb5f4c5f7?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=1650&q=80'
        ]);
    }
}
