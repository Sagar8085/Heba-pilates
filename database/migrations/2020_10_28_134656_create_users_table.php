<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->index()->default(4); // Default for a member is 4.
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number', 58)->nullable();
            $table->string('gender', 25)->nullable();
            $table->string('password');
            $table->string('api_token')->unique()->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->integer('age')->index()->nullable();
            $table->string('street_address')->nullable();
            $table->string('city', 185)->nullable();
            $table->string('postcode', 185)->nullable();
            $table->tinyInteger('has_rated_app')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
