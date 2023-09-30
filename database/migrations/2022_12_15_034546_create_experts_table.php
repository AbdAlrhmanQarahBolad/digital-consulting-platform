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
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('imgpath')->nullable() ;
            $table->string('phonenumber')->nullable();
            $table->string('address') ;
            $table->float('value_of_rating',2,1)->default(0.0);

           // $table->foreignId('user_id')->constrained('users');
           // $table->foreignId('user_id')->references('id')->on('users') ;
            $table->unsignedBigInteger('user_id')->references('id')->on('users')->default(0);
            //$table->integer('user_id') ;
            $table->string('experinces') ;
            $table->string('password');
            $table->integer('wallet')->default(0) ;




            $table->rememberToken();
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
        Schema::dropIfExists('experts');
    }
};
