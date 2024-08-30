<?php

namespace App\Console\Commands;

use App\Models\OnDemandCategory;
use Illuminate\Console\Command;

class MigrateOnDemandCategoryGyms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:odcategorygyms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a one time use command. The purpose of this is to give all on demand categories access to the first 3 UK gyms. This is their current access level however new features will allow them to change this per category';

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
        $categories = OnDemandCategory::all();

        foreach ($categories as $category) {
            $category->gyms()->sync([1, 2, 3]);
        }
    }
}
