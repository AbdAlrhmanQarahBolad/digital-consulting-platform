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
        Schema::create('times', function (Blueprint $table) {
            $table->id();

            $table->time('sundayS');
            $table->time('sundayE');
            $table->time('mondayS');
            $table->time('mondayE');
            $table->time('tuesdayS');
            $table->time('tuesdayE');
            $table->time('wednesdayS');
            $table->time('wednesdayE');
            $table->time('thursdayS');
            $table->time('thursdayE');



            $table->integer('expert_id')->references('id')->on('experts');

          //  $table->foreignId('reserved_times_id')->references('id')->on('reserved__times');



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
        Schema::dropIfExists('times');
    }
};
