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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('m_id')->references('id')->on('medical__consultations');
            // $table->foreignId('p_id')->references('id')->on('psychological__consultations');
            // $table->foreignId('f_id')->references('id')->on('family__consultations');
            // $table->foreignId('v_id')->references('id')->on('vocational__consultations');
            // $table->foreignId('b_id')->references('id')->on('business__management__consultations');
            $table->integer('m_id')->references('id')->on('medical__consultations')->onUpdate('cascade')
            ->onDelete('cascade')->nullable();



            $table->integer('p_id')->references('id')->on('psychological__consultations') ->onUpdate('cascade')
            ->onDelete('cascade')->nullable() ;

            $table->integer('f_id')->references('id')->on('family__consultations')->nullable();


            $table->integer('v_id')->references('id')->on('vocational__consultations') ->onUpdate('cascade')
            ->onDelete('cascade')->nullable();


            $table->integer('b_id')->references('id')->on('business__management__consultations') ->onUpdate('cascade')
            ->onDelete('cascade')->nullable();

            $table->integer('expert_id')->references('id')->on('experts') ->onUpdate('cascade')
            ->onDelete('cascade')->nullable();

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
        Schema::dropIfExists('consultations');
    }
};
