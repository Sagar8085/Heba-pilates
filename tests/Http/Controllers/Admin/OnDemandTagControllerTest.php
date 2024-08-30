<?php

namespace Tests\Http\Controllers\Admin;

use App\Models\OnDemand;
use App\Models\Tag;
use Illuminate\Http\Response;
use Str;
use Tests\Http\Controllers\ControllerTest;
use Tests\TestCase;

class OnDemandTagControllerTest extends ControllerTest
{
    /** @test */
    public function it_can_return_all_tags()
    {
        $tags = Tag::factory()->count(5)->create();

        $this->index('ondemand.tags.index')
            ->assertSuccessful()
            ->assertJson([
                'status' => __('response.success'),
                'data' => $tags->toArray(),
            ]);
    }

    /** @test */
    public function it_returns_an_ondemands_tags()
    {
        $ondemand = OnDemand::factory()->create();
        $tag = Tag::factory()->create();
        $ondemand->tags()->attach($tag);

        $this->show('ondemand.tags.show', $ondemand)
            ->assertSuccessful()
            ->assertJson([
                'status' => __('response.success'),
                'data' => $ondemand->tags->toArray(),
            ]);
    }

    /** @test */
    public function it_successfully_creates_a_new_tag()
    {
        $ondemand = OnDemand::factory()->create();

        $response = $this->store('ondemand.tags.store', [
            'tag' => 'Test tag',
            'ondemand_id' => $ondemand->id,
        ]);

        $tagId = $response->json('tag.id');

        $response->assertSuccessful();

        $this->assertDatabaseHas('tags', [
            'name' => 'Test tag',
            'slug' => 'test-tag',
        ]);

        $this->assertDatabaseHas('_pivot_on_demand_tags', [
            'ondemand_id' => $ondemand->id,
            'tag_id' => $tagId,
        ]);
    }

    /** @test */
    public function it_requires_a_tag_to_create_a_new_tag()
    {
        $this->store('ondemand.tags.store')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tag');
    }

    /** @test */
    public function it_requires_an_ondemand_id_to_create_a_new_tag()
    {
        $this->store('ondemand.tags.store')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('ondemand_id');
    }

    /** @test */
    public function it_requires_an_existing_ondemand_id_to_create_a_new_tag()
    {
        $this->store('ondemand.tags.store', [
            'ondemand_id' => -1,
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('ondemand_id');
    }

    /** @test */
    public function it_requires_a_tag_string_to_create_a_new_tag()
    {
        $this->store('ondemand.tags.store', [
            'tag' => 99,
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tag');
    }

    /** @test */
    public function it_has_a_max_tag_length_of_200()
    {
        $this->store('ondemand.tags.store', [
            'tag' => Str::random(201),
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tag');
    }

    /** @test */
    public function it_saves_on_demand_tags()
    {
        $ondemand = OnDemand::factory()->create();
        $tags = Tag::factory()->count(5)->create();

        $response = $this->update('ondemand.tags.update', $ondemand, [
            'tags' => $tags->toArray(),
        ]);

        $response->assertSuccessful();

        $tags->each(fn ($tag) => $this->assertDatabaseHas('_pivot_on_demand_tags', [
            'ondemand_id' => $ondemand->id,
            'tag_id' => $tag->id,
        ])
        );
    }

    /** @test */
    public function it_requires_an_array_of_tags_to_update()
    {
        $ondemand = OnDemand::factory()->create();
        $tags = Tag::factory()->count(5)->create();

        $this->update('ondemand.tags.update', $ondemand)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tags');
    }

    /** @test */
    public function it_requires_an_array_of_tag_ids_to_update()
    {
        $ondemand = OnDemand::factory()->create();

        $this->update('ondemand.tags.update', $ondemand, [
            'tags' => [
                ['slug' => 'test'],
            ],
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tags.0.id');
    }

    /** @test */
    public function it_requires_an_existing_tag_to_update()
    {
        $ondemand = OnDemand::factory()->create();

        $this->update('ondemand.tags.update', $ondemand, [
            'tags' => [
                ['id' => -1],
            ],
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('tags.0.id');
    }
}
