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
        Schema::table('providers', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->text('detail')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('url')->nullable()->change();
            $table->string('email')->nullable()->change();
        });

        Schema::table('chesses', function (Blueprint $table) {
            $table->string('complex_feed_name')->nullable()->change();
            $table->string('building_feed_name')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('scheme')->nullable()->change();
            $table->json('entrances_data')->nullable()->change();
        });

        Schema::table('feeds', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->text('detail')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('url')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
        });

        Schema::table('chesses', function (Blueprint $table) {
            $table->string('complex_feed_name')->nullable(false)->change();
            $table->string('building_feed_name')->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
            $table->string('scheme')->nullable(false)->change();
            $table->json('entrances_data')->nullable(false)->change();
        });

        Schema::table('feeds', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });
    }
};
