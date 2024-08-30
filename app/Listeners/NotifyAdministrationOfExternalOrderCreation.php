<?php

namespace App\Listeners;

use App\Events\OrderCreatedExternally;
use App\Mail\SendEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotifyAdministrationOfExternalOrderCreation
{
    public function handle(OrderCreatedExternally $event): void
    {
        $order = $event->getOrder();

        /** @var User $user */
        $user = $order->member;

        $mail = new SendEmail(
            'hello@hebapilates.com',
            'emails.user.user-created-externally',
            'New User Created Externally',
            [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'order_no' => '#' . $order->getKey(),
                'credits' => $order?->orderable?->studio_credits,
            ],
            'hello@hebapilates.com'
        );

        Mail::to('hello@hebapilates.com')->send($mail);
    }
}
