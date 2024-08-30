<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone_number');
            $table->timestamp('date_of_birth');
            $table->string('gender');

            $table->enum('status', ['new', 'contacted', 'nurturing', 'unqualified', 'lost', 'won'])->default('new');
            $table->enum('temperature', ['cold', 'warm', 'hot'])->default('warm');

            $table->integer('assigned_to')->nullable();
            $table->timestamp('assigned_at')->nullable();

            $table->string('source')->nullable();
            $table->string('gym_locations')->nullable();
            $table->string('heard_from')->nullable();
            $table->string('fitness_history')->nullable();
            $table->string('previous_gyms')->nullable();
            $table->string('last_exercise')->nullable();
            $table->string('upcoming_events')->nullable();
            $table->string('family_situation')->nullable();
            $table->string('sleep_pattern')->nullable();
            $table->string('fitness_activities')->nullable();
            $table->string('fitness_goal')->nullable();

            $table->integer('gym_id')->nullable();



            $table->boolean('interested')->default(true);


            $table->boolean('subscribe_weekly')->default(false);
            $table->boolean('subscribe_monthly')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
