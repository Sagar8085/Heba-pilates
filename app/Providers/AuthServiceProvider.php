<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->superadmin) {
                return true;
            }

            /** Dynamic gate to check an admin privilege. */
            if ($ability !== 'administrator') {
                return in_array($ability, $user->privileges) ? true : false;
            }
        });

        Gate::define('administrator', function (User $user) {
            if (in_array($user->role_id, [1, 2, 3])) {
                return true;
            }

            return false;
        });
    }
}
