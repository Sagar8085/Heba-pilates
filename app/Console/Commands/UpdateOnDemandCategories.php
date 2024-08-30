<?php

namespace App\Console\Commands;

use App\Models\OnDemand;
use Illuminate\Console\Command;

class UpdateOnDemandCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:odcats';

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
        $rows = OnDemand::all();

        foreach ($rows as $row) {
            $row->categories()->sync([$row->category_id => ['order' => $row->order]]);
        }
    }
}
