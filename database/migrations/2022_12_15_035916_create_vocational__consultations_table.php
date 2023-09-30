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
        Schema::create('vocational__consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expert_id')->references('id')->on('experts');
            $table->unsignedBigInteger('consultation_id')->references('id')->on('consultations');
            $table->integer('time_of_consultaion_v')->default(0);
            $table->integer('cost_v');
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
        Schema::dropIfExists('vocational__consultations');
    }
};
