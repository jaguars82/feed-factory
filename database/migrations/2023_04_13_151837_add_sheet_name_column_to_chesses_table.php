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
            $table->string('sheet_name')->nullable()->after('scheme');
            $table->integer('sheet_index')->nullable()->after('sheet_name');
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
            $table->dropColumn('sheet_name');
            $table->dropColumn('sheet_index');
        });
    }
};
