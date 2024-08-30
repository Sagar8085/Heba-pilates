<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;
use App\Models\Subscription;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->createDefaultAdminAccount();
        $this->call([
            SubscriptionTierSeeder::class,
            UserSeeder::class,
        ]);
    }

    /**
     * Create the default administrator account.
     *
     * @param none
     *
     * @return Void
     */
    private function createDefaultAdminAccount() {
        User::factory(1)->state([
            'role_id' => 1,
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@olivex.co.uk',
            'phone_number' => '07480305234',
            'gender' => 'Male',
            'password' => Hash::make('0946Duck'),
            'date_of_birth' => '1995-07-14'
        ])->create();
    }
}
