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
        Schema::create('ranged_permit_generators', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('reason');
            $table->string('reason_category');
            $table->date('from_date');
            $table->date('to_date');
            $table->bigInteger('fkSantri_id')->unsigned();
            $table->bigInteger('fkPresenceGroup_id')->unsigned();

            $table->foreign('fkSantri_id')->references('id')->on('santris')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fkPresenceGroup_id')->references('id')->on('presence_groups')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ranged_permit_generators');
    }
};
