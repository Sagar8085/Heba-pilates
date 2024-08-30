<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class UsersExport
 *
 * @package App\Exports;
 */
class UsersExport implements FromCollection, WithHeadings
{
    private Collection $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection(): Collection
    {
        return $this->users->map(fn (User $user) => [
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->phone_number,
            optional($user->lead)->appointment ?: '---',
            $user->created_at_human,
            $user->gym?->name ?: '---',
            $user->guest_status,
            $user->subscription_status_human,
            $user->subscription?->name,
            $user->creditPackPurchase ? $user->creditPackPurchase->pack->name : '---',
            $user->creditPackPurchase ? $user->creditPackPurchase->expires_human : '---',
            $user->calls_made ?: '---',
            $user->emails_sent ?: '---',
            $user->available_credits ?: '---',
            $user->mostRecentPastReformerBooking ? $user->mostRecentPastReformerBooking->formatted_csv_date : '---',
            $user->lastActivityCall ? $user->lastActivityCall->datetime->format('dS F Y \a\t h:iA') : '---',
            $user->lastSentEmail ? $user->lastSentEmail->created_at->format('dS F Y \a\t h:iA') : '---',
            optional($user->lead)->source ?: '---',
            number_format($user->total_spend ?: 0, 2),
            number_format($user->expected_future_value ?: 0, 2),
            $user->parq ? 'Completed' : 'Not Completed',
            $user->next_session ? $user->next_session->formatted_csv_date : '---',
            $user->total_studio_visits ?: '---',
            $user->visits_per_week ?: '---',
            Str::ucfirst($user->gender) ?: '---',
            $user->dob_human ?: '---',
            $user->age ?: '---',
            $user->memberProfile?->pilates_experience ?: '---',
            $user->memberProfile?->fitness_level ?: '---',
        ]);
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email Address',
            'Phone Number',
            'Appointment',
            'Profile Registered',
            'Hub Studio',
            'Guest Status',
            'Subscription Status',
            'Subscription Type',
            'Credit Pack Type',
            'Credit Pack Expiry',
            'No. of Calls Made',
            'No. of Emails Sent',
            'Credits Available',
            'Last Visit',
            'Last Call Date',
            'Last Email Date',
            'Lead Source',
            'Lifetime Value',
            'Expected Future Value',
            'PARQ',
            'Next Session',
            'No. of Visits',
            'Visit Frequency',
            'Gender',
            'Date of Birth',
            'Age',
            'Pilates Experience',
            'Fitness Level',
        ];
    }
}
