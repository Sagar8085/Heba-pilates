<?php

namespace App\Jobs;

use App\Models\PushToken;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipient;
    public $title;
    public $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $title, $message)
    {
        $this->recipient = $recipient;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new GuzzleClient();

        /**
         * Load expo token for recipient.
         */
        $pushToken = PushToken::where('user_id', $this->recipient->id)->orderBy('id', 'DESC')->first();

        if ($pushToken !== null) {

            $arrExpoData = collect([
                'to' => $pushToken->token,
                'title' => $this->title,
                'body' => $this->message,
                'sound' => 'default',
            ]);

            $client->post('https://exp.host/--/api/v2/push/send', [
                'json' => $arrExpoData,
            ]);

        }
    }
}
