<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateDemoAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:demoadmin';

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
        User::factory(1)->state([
            'role_id' => 1,
            'first_name' => 'Volution',
            'last_name' => 'Demo',
            'email' => 'demo@olivex.co.uk',
            'phone_number' => '0161 123 0821',
            'gender' => 'Male',
            'password' => Hash::make('demo'),
            'date_of_birth' => '1995-07-14',
        ])->create();
    }
}
