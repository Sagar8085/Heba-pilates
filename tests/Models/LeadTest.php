<?php

namespace Tests\Models;

use App\Collections\LeadCollection;
use App\Models\Lead;
use App\Models\User;
use Tests\TestCase;

/**
 * Class LeadTest
 *
 * @package Tests\Models
 */
class LeadTest extends TestCase
{
    /** @test */
    public function it_returns_a_lead_collection(): void
    {
        $this->assertInstanceOf(LeadCollection::class, Lead::all());
    }

    /** @test */
    public function it_can_be_assigned_to_a_user(): void
    {
        $assignee = $this->createAdministrator();

        /** @var Lead $lead */
        $lead = $this->createLead(null, [
            'assigned_to' => $assignee->id,
        ]);

        $this->assertTrue($lead->assigned->is($assignee));
    }

    /** @test */
    public function it_can_be_not_assigned_to_a_user(): void
    {
        /** @var Lead $lead */
        $lead = $this->createLead(null, [
            'assigned_to' => null,
        ]);

        $this->assertTrue($lead->assigned->isNot(new User));
    }
    
    /** @test */
    public function a_lead_can_be_without_a_conversion(): void
    {
        $this->createLead(5);
        $this->createLead(5, [
            'assigned_to' => null,
        ]);

        /** @var LeadCollection $leads */
        $leads = Lead::all();

        $this->assertCount(5, $leads->withoutConversions());
    }

    /** @test */
    public function a_lead_can_be_for_a_conversion_to_subscription(): void
    {
        $this->createLead(5, [
            'subscribe_weekly' => 1,
        ]);
        $this->createLead(5, [
            'assigned_to' => null,
        ]);

        /** @var LeadCollection $leads */
        $leads = Lead::all();

        $this->assertCount(5, $leads->convertedToSubscription());
    }

    /** @test */
    public function a_lead_can_be_for_a_conversion_to_credit_pack(): void
    {
        $this->createLead(5, [
            'subscribe_monthly' => 0,
            'subscribe_weekly' => 0,
        ]);
        $this->createLead(5, [
            'assigned_to' => null,
        ]);

        /** @var LeadCollection $leads */
        $leads = Lead::all();

        $this->assertCount(5, $leads->convertedToCreditPack());
    }
}