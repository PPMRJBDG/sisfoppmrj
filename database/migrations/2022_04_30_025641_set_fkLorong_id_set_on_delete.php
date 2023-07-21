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
        Schema::table('santris', function (Blueprint $table) 
        {
            $table->dropForeign(['fkLorong_id']);
            $table->foreign('fkLorong_id')->references('id')->on('lorongs')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('santris', function (Blueprint $table) 
        {
            $table->dropForeign(['fkLorong_id']);
            $table->foreign('fkLorong_id')->references('id')->on('lorongs');
        });
    }
};
