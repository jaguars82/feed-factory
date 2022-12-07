<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->foreignId('feed_id')->nullable()->change();
            $table->foreignId('transport_id')->nullable()->change();
            $table->tinyInteger('last_completed_formstep')->unsigned()->nullable()->after('attachment_filename');
            $table->boolean('is_configuration_complete')->default(false)->after('last_completed_formstep');
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
            $table->dropColumn('is_configuration_complete');
            $table->dropColumn('last_completed_formstep');
            $table->foreignId('transport_id')->nullable(false)->change();
            $table->foreignId('feed_id')->nullable(false)->change();
        });
    }
};
