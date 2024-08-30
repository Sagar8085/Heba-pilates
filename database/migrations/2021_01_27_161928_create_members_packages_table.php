<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members_packages', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->index();
            $table->integer('trainer_id')->index();
            $table->integer('package_id')->index();
            $table->datetime('expires')->nullable();
            $table->integer('total')->nullable();
            $table->integer('remaining')->nullable();
            $table->integer('length')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members_packages');
    }
}
