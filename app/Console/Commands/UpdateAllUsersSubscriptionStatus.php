<?php

namespace App\Console\Commands;

use App\Jobs\UpdateUserSubscriptionStatus;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateAllUsersSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:substatus';

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
        $users = User::all();

        foreach ($users as $user) {
            UpdateUserSubscriptionStatus::dispatch($user)->onQueue('subscription-status');
        }
    }
}
