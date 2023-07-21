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
        if(!Schema::hasTable('presence_groups'))
            Schema::create('presence_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('status', ['active', 'inactive']);
                $table->time('start_hour');
                $table->time('end_hour');
                $table->string('days');
                $table->boolean('show_summary_at_home');
                $table->timestamps();
            });

        if(!Schema::hasTable('presences'))
            Schema::create('presences', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->dateTime('start_date_time')->nullable();
                $table->dateTime('end_date_time')->nullable();
                $table->foreignId('fkPresence_group_id')->nullable();
                $table->timestamps();

                $table->foreign('fkPresence_group_id')->references('id')->on('presence_groups')->onDelete('cascade')->onUpdate('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presences');
        Schema::dropIfExists('presence_groups');
    }
};
