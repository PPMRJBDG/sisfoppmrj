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
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('pageNumbers');
            $table->timestamps();
        });

        Schema::create('monitoring_materis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('pageNumber');
            $table->bigInteger('fkSantri_id')->unsigned();
            $table->bigInteger('fkMateri_id')->unsigned();
            $table->timestamps();

            $table->foreign('fkMateri_id')->references('id')->on('materis')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('materis');
        Schema::dropIfExists('monitoring_materis');
    }
};
