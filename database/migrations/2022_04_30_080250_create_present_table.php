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
        if(!Schema::hasTable('presents'))
            Schema::create('presents', function (Blueprint $table) {
                $table->bigInteger('fkPresence_id')->unsigned();
                $table->bigInteger('fkSantri_id')->unsigned();
                $table->timestamps();

                $table->primary(['fkPresence_id', 'fkSantri_id']);
                $table->foreign('fkPresence_id')->references('id')->on('presences')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('fkSantri_id')->references('id')->on('santris')->onDelete('cascade')->onUpdate('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presents');
    }
};
