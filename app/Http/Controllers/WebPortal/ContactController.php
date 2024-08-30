<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function sendEmail(Request $request)
    {
        $recipient = new \stdClass();
        $recipient->email = 'hello@hebapilates.com';

        SendEmailJob::dispatch($recipient, 'emails.contact', 'Contact Form Submission',
            ['email' => $request->email, 'topic' => $request->topic, 'message' => $request->message]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
