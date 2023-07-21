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
        Schema::table('presence_groups', function($table)
        {
            $table->string('start_hour')->nullable()->change();
            $table->string('end_hour')->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presence_groups', function($table)
        {
            $table->string('start_hour')->nullable(false)->change();
            $table->string('end_hour')->nullable(false)->change();
        });
    }
};
