<?php

namespace Tests\Http\Controllers\Admin\Membership;

use App\Models\Privilege;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\Http\Controllers\ControllerTest;

class MembershipControllerUpdateTest extends ControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2020, 1, 1));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /** @test */
    public function it_successfully_updates_a_membership()
    {
        $subscription = Subscription::factory()->create([
            'expires' => Carbon::now(),
        ]);
        $expiry = Carbon::now()->addMonth();

        $this
            ->update(
                'billing.memberships.update',
                $subscription,
                ['expires' => $expiry->toISOString()],
            )
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'expires_human',
                    'member' => [
                        'id',
                        'name',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'expires' => $expiry->endOfDay()->toDateTimeString(),
        ]);
    }

    /**
     * @test
     * @dataProvider expiryProvider
     */
    public function it_requires_a_valid_expiry_date($expires)
    {
        $subscription = Subscription::factory()->create([
            'expires' => Carbon::now(),
        ]);

        $this
            ->update(
                'billing.memberships.update',
                $subscription,
                ['expires' => $expires],
            )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('expires');
    }

    public function expiryProvider()
    {
        return [
            'null' => ['expires' => null],
            'invalid-date' => ['expires' => 'not-a-date'],
        ];
    }
}
