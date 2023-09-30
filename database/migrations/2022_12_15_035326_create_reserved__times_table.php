<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserved__times', function (Blueprint $table) {
            $table->id();
            $table->time('start_of_time_reserved')->nullable();
            $table->time('end_of_time_reserved')->nullable();
            $table->integer('number_of_day')->nullable();
            $table->integer('expert_id')->references('id')->on('experts')->nullable();
            $table->integer('user_id')->references('id')->on('users')->nullable();

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
        Schema::dropIfExists('reserved__times');
    }
};
