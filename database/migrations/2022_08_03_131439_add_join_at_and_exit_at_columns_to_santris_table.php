<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Santri;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->date('join_at')->nullable(true);
            $table->date('exit_at')->nullable(true);            
        });

        Santri::query()->update(['join_at' => '2022-05-05']);

        Schema::table('santris', function (Blueprint $table) {
            $table->date('join_at')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('santris', function (Blueprint $table) {
            Schema::table('santris', function (Blueprint $table) {
                $table->dropColumn('join_at');
                $table->dropColumn('exit_at');
            });
        });
    }
};
