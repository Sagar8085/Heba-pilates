<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class StripeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StripeClient::class, function () {
            return new StripeClient(['api_key' => config('services.stripe.secret')]);
        });
    }
}
