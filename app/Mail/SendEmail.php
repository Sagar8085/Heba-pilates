<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;
    public $template;
    public $subject;
    public $props;
    public $reply_to;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($recipient, $template, $subject, $props, $replyTo)
    {
        $this->recipient = $recipient;
        $this->template = $template;
        $this->subject = $subject;
        $this->props = $props;
        $this->reply_to = $replyTo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->template)
            ->subject($this->subject)
            ->from('noreply-hebepilates@olivex.co.uk', 'Heba Pilates')
            ->replyTo($this->reply_to)
            ->with([
                'recipient' => $this->recipient,
                'props' => $this->props,
            ]);
    }
}
