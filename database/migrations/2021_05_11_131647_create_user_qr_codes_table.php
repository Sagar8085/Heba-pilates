<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserQrCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('identifier')->index();
            $table->datetime('expires_at')->nullable();
            $table->datetime('scanned_at')->index()->nullable();
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
        Schema::dropIfExists('user_qr_codes');
    }
}
