<?php

namespace App\Console\Commands;

use App\Models\PARQ;
use Illuminate\Console\Command;

class UpdatePARQCreator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parq:creator';

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
        $parqs = PARQ::get();

        foreach ($parqs as $parq) {
            $parq->update([
                'created_by' => $parq->user_id,
            ]);
        }
    }
}
