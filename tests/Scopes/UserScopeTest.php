<?php

namespace Tests\Scopes;

use App\Models\User;
use Tests\TestCase;

/**
 * Class UserScopeTest
 *
 * @package Tests\Scopes;
 */
class UserScopeTest extends TestCase
{
    /** @test */
    public function it_scopes_the_users_with_unassigned_leads(): void
    {
        $this->createLead(15, [
            'assigned_to' => null,
        ]);

        $this->createLead(10);

        $this->assertCount(
            15,
            User::query()->withUnassignedLeads()->get()
        );
    }

    /** @test */
    public function it_scopes_the_users_with_assigned_leads(): void
    {
        $this->createLead(15, [
            'assigned_to' => null,
        ]);

        $this->createLead(10);

        $this->assertCount(
            10,
            User::query()->withAssignedLeads()->get()
        );
    }
    
    /** @test */
    public function it_scopes_the_users_without_the_currently_authenticated_user(): void
    {
        $this->actingAs($this->createUser([], 20)->first());
        $this->assertCount(19, User::query()->withoutCurrentUser()->get());
    }
}