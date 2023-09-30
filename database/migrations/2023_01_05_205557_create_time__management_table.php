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
        Schema::create('time__management', function (Blueprint $table) {
            $table->id();
            $table->integer('expert_id')->references('id')->on('experts')->nullable() ;
            $table->integer('num_of_day')->nullable();
            $table->time('start')->nullable();
            $table->time('end')->nullable();

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
        Schema::dropIfExists('time__management');
    }
};
