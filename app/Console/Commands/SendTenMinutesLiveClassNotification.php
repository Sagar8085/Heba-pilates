<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\LiveClassBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTenMinutesLiveClassNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livereminder:tenminutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $startTime = Carbon::now()->addMinutes(10)->format('Y-m-d H:i') . ':00';

        $bookings = LiveClassBooking::join('live_classes', 'live_classes.id', 'live_classes_bookings.liveclass_id')
            ->where('live_classes.datetime', $startTime)
            ->get();

        foreach ($bookings as $booking) {
            SendEmailJob::dispatch($booking->member, 'emails.user.liveclass-upcoming',
                $booking->class->name . ' In 10 Minutes',
                ['liveclass' => $booking->class, 'user' => $booking->member])->onQueue('account-notifications');
            SendPushNotification::dispatch($booking->member, '10 Minutes To Go',
                'Your live class starts soon at ' . $booking->class->time_human)->onQueue('account-notifications');
        }
    }
}
