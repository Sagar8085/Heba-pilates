<?php

namespace App\Console\Commands;

use App\Models\Gym;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AddClaphamBookingDetails extends Command
{
    protected $signature = 'heba:clapham';

    protected $description = 'Synchronise the booking classes for Clapham gym';

    public function handle()
    {
        $gym = Gym::query()
            ->where([
                ['corporate_name', 'clapham'],
            ])
            ->first();

        collect(range(1, 8))
            ->each(fn (int $iteration) => $gym->reformers()->updateOrCreate(
                [
                    'name' => 'NU #' . $iteration,
                ],
                [
                    'status' => 'working',
                ],
            ))
            ->pipe(fn (Collection $collection) => $this->info($collection->count() . ' reformers synchronised'));
    }
}
