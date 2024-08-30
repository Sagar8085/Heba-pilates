<?php

namespace Tests\Http\Request\Member;

use App\Http\Requests\Member\StoreMembershipRequest;
use Tests\TestCase;

class StoreMembershipRequestTest extends TestCase
{
    /**
     * @test
     * @dataProvider typeDataProvider
     */
    public function it_determines_if_the_membership_has_already_been_paid_for($type, $paid)
    {
        $request = new StoreMembershipRequest();
        $request->merge(['type' => $type]);

        $this->assertEquals($paid, $request->hasAlreadyPaid());
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function it_determines_the_event_for_free_memberships($type, $event)
    {
        $request = new StoreMembershipRequest();
        $request->merge(['type' => $type]);

        $this->assertEquals($event, $request->event());
    }

    public function typeDataProvider()
    {
        return [
            'free' => [
                'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_FREE,
                'paid' => false,
            ],
            'already-paid' => [
                'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_PAID,
                'paid' => true,
            ],
            'bacs' => [
                'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_BACS,
                'paid' => false,
            ],
        ];
    }

    public function eventProvider()
    {
        return [
            'free' => [
                'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_FREE,
                'event' => 'Free Membership Added via Admin Panel',
            ],
            'already-paid' => [
                'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_PAID,
                'event' => 'Already-Paid Membership added via Admin Panel',
            ],
            'bacs' => [
                'type' => StoreMembershipRequest::MEMBERSHIP_TYPE_BACS,
                'event' => 'Purchased Membership via Admin Panel',
            ],
        ];
    }
}
