<?php

namespace App\Console\Commands;

use App\Models\ReformerBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReformerBookingOneHourReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reformerbooking:onehourreminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a push notification to a user if they have a Reformer Booking in 1 hour.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now()->addMinutes(60)->format('Y-m-d H:i') . ':00';

        $bookings = ReformerBooking::where('datetime', $startTime)->get();

        foreach ($bookings as $booking) {
            SendPushNotification::dispatch($booking->member, 'Upcoming Session',
                'Reminder: your Heba Pilates session at ' . $booking->reformer->gym->name . ' will begin at ' . $booking->time_human)->onQueue('account-notifications');
        }
    }
}
