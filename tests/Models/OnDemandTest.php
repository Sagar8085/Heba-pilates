<?php

namespace Tests\Models;

use App\Models\OnDemand;
use App\Models\Tag;
use Tests\TestCase;

class OnDemandTest extends TestCase
{
    /** @test */
    public function it_can_have_tags()
    {
        $ondemand = OnDemand::factory()->create();
        $tag = Tag::factory()->create();

        $ondemand->tags()->attach($tag);

        $this->assertCount(1, $ondemand->tags);
    }

    /** @test */
    public function it_can_be_scoped_by_tag_slugs()
    {
        $ondemandA = OnDemand::factory()->create();
        $ondemandB = OnDemand::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $ondemandA->tags()->attach($tags);

        $fetched = OnDemand::filterTagBySlugs($tags->pluck('slug')->toArray())->get();

        $this->assertCount(1, $fetched);
        $this->assertEquals($ondemandA->id, $fetched->first()->id);
    }

    /** @test */
    public function it_only_returns_all_matching_when_scoped_by_tag_slugs()
    {
        $ondemandA = OnDemand::factory()->create();
        $ondemandB = OnDemand::factory()->create();
        $tagA = Tag::factory()->create();
        $tagB = Tag::factory()->create();

        $ondemandA->tags()->attach($tagA);
        $ondemandA->tags()->attach($tagB);
        $ondemandB->tags()->attach($tagA);

        $slugs = collect([$tagA, $tagB])->pluck('slug')->toArray();
        $fetched = OnDemand::filterTagBySlugs($slugs)->get();

        $this->assertCount(1, $fetched);
        $this->assertEquals($ondemandA->id, $fetched->first()->id);
    }

    /** @test */
    public function it_returns_no_results_when_no_tag_slugs_match()
    {
        $ondemandA = OnDemand::factory()->create();
        $tagA = Tag::factory()->create();

        $fetched = OnDemand::filterTagBySlugs([$tagA->slug])->get();

        $this->assertCount(0, $fetched);
    }
}
