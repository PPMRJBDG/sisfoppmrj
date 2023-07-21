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
        if(!Schema::hasTable('lorongs'))
            Schema::create('lorongs', function (Blueprint $table) 
            {
                $table->id();
                $table->string('name');
                $table->foreignId('fkSantri_leaderId');               
                $table->timestamps();
            });

        if(!Schema::hasTable('santris'))
            Schema::create('santris', function (Blueprint $table) 
            {
                $table->id();
                $table->foreignId('fkUser_id');
                $table->foreignId('fkLorong_id');
                $table->integer('angkatan');
                $table->string('nis');            
                $table->timestamps();

                $table->foreign('fkUser_id')->references('id')->on('users');
                $table->foreign('fkLorong_id')->references('id')->on('lorongs');
            });

        Schema::table('lorongs', function (Blueprint $table) 
        {
            $table->foreign('fkSantri_leaderId')->references('id')->on('santris');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lorongs', function (Blueprint $table) 
        {
            $table->dropForeign(['fkSantri_leaderId']);
        });
        
        Schema::table('santris', function (Blueprint $table) 
        {
            $table->dropForeign(['fkLorong_id']);
        });

        Schema::dropIfExists('lorongs');
        Schema::dropIfExists('santris');
    }
};
