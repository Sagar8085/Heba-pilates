<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipient;
    public $template;
    public $subject;
    public $props;
    public $reply_to;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $template, $subject, $props, $replyTo = null)
    {
        $this->recipient = $recipient;
        $this->template = $template;
        $this->subject = $subject;
        $this->props = $props;

        if ($replyTo === null) {
            $this->reply_to = 'hello@hebapilates.com';
        } else {
            $this->reply_to = $replyTo;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (is_array($this->recipient)) {
            $to = $this->recipient[0];
            $cc = array_shift(0);
            // Remove to from cc list
        }
        {
            $to = $this->recipient;
            $cc = [];
        }

        Mail::to($this->recipient->email)->cc($cc)->send(new SendEmail($this->recipient, $this->template,
            $this->subject, $this->props, $this->reply_to));
    }
}
