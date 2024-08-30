<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\GuestsMail;
use App\Jobs\SendEmailJob;
use Mail;

class SendEmailController extends Controller
{
    public function index()
    {
      
        // SendEmailJob::dispatch(['email' => 'shubham.sen@techvalens.com'], 'emails.user.profile-welcome-email', 'Welcome to Heba Pilates',
        // )->onQueue('account-notifications');


        
    Mail::to('shubham.sen@techvalens.com')->send(new GuestsMail());
 
     if (Mail::failures()) {
        return response()->json([
            'status' => 'failed',
        ]);
      }else{
        return response()->json([
            'status' => 'success',
        ]);
       }
     } 
}
