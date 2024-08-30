<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToParqResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parq_responses', function (Blueprint $table) {
            $table->string('current_injuries_details')->nullable()->after('current_injuries');
            $table->string('taking_medication_details')->nullable()->after('taking_medication');
            $table->string('currently_pregnant_details')->nullable()->after('currently_pregnant');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parq_responses', function (Blueprint $table) {
            $table->dropColumns(['current_injuries_details', 'taking_medication_details', 'currently_pregnant_details']);
        });
    }
}
