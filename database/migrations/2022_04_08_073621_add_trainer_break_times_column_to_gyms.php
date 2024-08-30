<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrainerBreakTimesColumnToGyms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->json('trainer_break_times')
                ->after('operating_hours')
                ->nullable(true)
                ->comment(
                    '{string: string[]} Day of week followed by array '
                    . 'of timeslots that trainers are on break for',
                );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropColumn('trainer_break_times');
        });
    }
}
