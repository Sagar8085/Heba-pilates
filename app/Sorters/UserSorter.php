<?php

namespace App\Sorters;

use Illuminate\Database\Query\JoinClause;

/**
 * Class UserSorter
 *
 * @package App\Sorters;
 */
class UserSorter extends Base
{
    protected array $sortables = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'appointment',
        'profile_registered',
        'hub_studio',
        'guest_status',
        'subscription_status',
        'subscription_type',
        'credit_pack_type',
        'credits_available',
        'last_visit',
        'next_session',
        'no_of_visits',
        'visit_frequency',
        'gender',
        'date_of_birth',
        'age',
        'pilates_experience',
        'fitness_level',
        'credit_pack_expiry',
        'no_of_calls_made',
        'no_of_emails_sent',
        'last_call_date',
        'last_email_date',
        'lead_source',
        'lifetime_value',
        'expected_future',
        'parq_status',
    ];

    public function firstName(string $direction)
    {
        return $this->builder->orderBy('first_name', $direction);
    }

    public function lastName(string $direction)
    {
        return $this->builder->orderBy('last_name', $direction);
    }

    public function email(string $direction)
    {
        return $this->builder->orderBy('email', $direction);
    }

    public function phoneNumber(string $direction)
    {
        return $this->builder->orderBy('phone_number', $direction);
    }

    public function appointment(string $direction)
    {
        return $this->builder->leftJoin('leads', 'users.id', '=', 'leads.user_id')
            ->leftJoin('leads_appointments', 'leads.id', '=', 'leads_appointments.lead_id')
            ->groupBy('users.id')
            ->orderBy('leads_appointments.datetime', $direction);
    }

    public function profileRegistered(string $direction)
    {
        $this->builder->orderBy('created_at', $direction);
    }

    public function hubStudio(string $direction)
    {
        $this->builder->leftJoin('members', 'users.id', '=', 'members.user_id')
            ->leftJoin('gyms', 'members.home_studio_id', '=', 'gyms.id')
            ->groupBy('users.id')
            ->orderBy('gyms.name', $direction);
    }

    public function guestStatus(string $direction)
    {
        $this->builder->orderBy('guest_status', $direction);
    }

    public function subscriptionStatus(string $direction)
    {
        $this->builder->orderBy('subscription_status', $direction);
    }

    public function subscriptionType(string $direction)
    {
        $this->builder->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->groupBy('users.id')
            ->orderBy('subscriptions.tier', $direction);
    }

    public function creditPackType(string $direction)
    {
        $this->builder->leftJoin('credit_packs_purchases', 'users.id', '=', 'credit_packs_purchases.user_id')
            ->leftJoin('credit_packs', 'credit_packs_purchases.credit_pack_id', '=', 'credit_packs.id')
            ->groupBy('users.id')
            ->orderBy('credit_packs.name', $direction);
    }

    public function creditsAvailable(string $direction)
    {
        $this->builder->withSum('subscriptions', 'studio_credits')
            ->withSum('creditPackPurchases', 'studio_credits')
            ->orderByRaw('(subscriptions_sum_studio_credits + credit_pack_purchases_sum_studio_credits) ' . $direction);
    }

    public function lastVisit(string $direction)
    {
        $this->builder->leftJoin('reformer_bookings', function (JoinClause $join) {
            $join->on('users.id', '=', 'reformer_bookings.user_id')
                ->where('datetime', '<', now());
        })
            ->groupBy('users.id')
            ->orderBy('reformer_bookings.datetime', $direction);
    }

    public function nextSession(string $direction)
    {
        $this->builder->leftJoin('reformer_bookings', function (JoinClause $join) {
            $join->on('users.id', '=', 'reformer_bookings.user_id')
                ->where('datetime', '>', now());
        })
            ->groupBy('users.id')
            ->orderBy('reformer_bookings.datetime', $direction);
    }

    public function noOfVisits(string $direction)
    {
        $this->builder->withCount('reformerBookings')
            ->orderBy('reformer_bookings_count', $direction);
    }

    public function visitFrequency(string $direction)
    {
        $this->builder->withCount('lastMonthReformerBookings')
            ->orderBy('last_month_reformer_bookings_count' . $direction);
    }

    public function gender(string $direction)
    {
        $this->builder->orderBy('gender', $direction);
    }

    public function dateOfBirth(string $direction)
    {
        $this->builder->orderBy('date_of_birth', $direction);
    }

    public function age(string $direction)
    {
        $this->builder->orderBy('age', $direction);
    }

    public function pilatesExperience(string $direction)
    {
        $this->builder->join('members', 'users.id', '=', 'members.user_id')
            ->orderBy('members.pilates_experience', $direction);
    }

    public function fitnessLevel(string $direction)
    {
        $this->builder->join('members', 'users.id', '=', 'members.user_id')
            ->orderBy('members.pilates_experience', $direction);
    }

    public function creditPackExpiry($direction)
    {
        $this->builder->leftJoin('credit_packs_purchases', 'users.id', '=', 'credit_packs_purchases.user_id')
            ->groupBy('users.id')
            ->orderBy('credit_packs_purchases.expires', $direction);
    }

    public function noOfCallsMade($direction)
    {
        $this->builder->withCount('leadActivityCalls')
            ->orderBy('lead_activity_calls_count', $direction);
    }

    public function noOfEmailsSent($direction)
    {
        $this->builder->withCount('sentEmails')
            ->orderBy('sent_emails_count', $direction);
    }

    public function lastCallDate($direction)
    {
        $this->builder->leftJoin('leads', 'users.id', '=', 'leads.user_id')
            ->leftJoin('leads_calls', 'leads_calls.lead_id', '=', 'leads.id')
            ->groupBy('users.id')
            ->orderBy('leads_calls.datetime');
    }

    public function lastEmailDate($direction)
    {
        $this->builder->leftJoin('leads', 'users.id', '=', 'leads.user_id')
            ->leftJoin('leads_activity_log', 'leads_activity_log.lead_id', '=', 'leads.id')
            ->groupBy('users.id')
            ->orderBy('leads_activity_log.created_at', $direction);
    }

    public function leadSource($direction)
    {
        $this->builder->leftJoin('leads', 'users.id', '=', 'leads.user_id')
            ->orderBy('leads.source', $direction);
    }

    public function lifetimeValue($direction)
    {
        $this->builder->orderBy('total_spend', $direction);
    }

    public function expectedFuture($direction)
    {
        $this->builder->orderBy('expected_future_value', $direction);
    }

    public function parqStatus($direction)
    {
        $this->builder->withCount('parq')
            ->orderBy('parq_count', $direction);
    }
}
