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
        Schema::table('chesses', function (Blueprint $table) {
            $table->json('color_legend')->nullable()->after('entrances_data')->comment('color indication in chess (flat statuses: "sale", "sold", "reserved", "unavailable")');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chesses', function (Blueprint $table) {
            $table->dropColumn('color_legend');
        });
    }
};
