<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class QueryPopularGym extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:preferredgym';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the most popular gym for a user by Reformers';

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
        User::chunk(200, function ($users) {
            foreach ($users as $user) {
                $user->updatePreferredStudio();
            }
        });
    }
}
