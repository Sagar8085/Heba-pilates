<?php

namespace App\Console\Commands;

use App\Jobs\GuestStatusJob;
use Illuminate\Console\Command;

class UpdateAllGuestStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:gueststatuses';

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
        GuestStatusJob::dispatch()->onQueue('guest-status');
    }
}
