<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MigrateAdminGyms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:admingyms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a one time use command. The purpose of this is to give all current administrators access to the first 3 UK gyms. This is their current access level however new features will allow them to change this per admin.';

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
        $admins = User::select('users.*')->onlyAdmins()->get();

        foreach ($admins as $admin) {
            $admin->gyms()->sync([1, 2, 3]);
        }
    }
}
