<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\LeadActivityType;
use Carbon\Carbon;

class LeadActivityTypesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Appointment booked', 'slug' => 'appointment-booked', 'image_path' => '/images/icons/appointment-24px.svg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Call made', 'slug' => 'call-made', 'image_path' => '/images/icons/call-24px.svg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Email sent', 'slug' => 'email', 'image_path' => '/images/icons/email-24px.svg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Note added', 'slug' => 'note', 'image_path' => '/images/icons/note-24px.svg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ];

        LeadActivityType::insert($types);
    }
}
