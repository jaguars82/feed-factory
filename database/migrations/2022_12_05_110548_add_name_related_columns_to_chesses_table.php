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
            $table->string('example_chess_path')->nullable()->after('is_active')->comment('path to chess file (excel) to use when setting up params in web interface');
            $table->string('file_chess_path')->nullable()->after('example_chess_path')->comment('path to chess file (excel) we get from provider an use to generate the feed');
            $table->string('developer_alias')->nullable()->after('building_feed_name');
            $table->string('complex_alias')->nullable()->after('developer_alias');
            $table->string('building_alias')->nullable()->after('complex_alias');
            $table->string('sender_email')->nullable()->after('file_chess_path');
            $table->string('attachment_filename')->nullable()->after('sender_email')->comment('filename in email attachment');
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
            $table->dropColumn('attachment_filename');
            $table->dropColumn('sender_email');
            $table->dropColumn('building_alias');
            $table->dropColumn('complex_alias');
            $table->dropColumn('developer_alias');
            $table->dropColumn('file_chess_path');
            $table->dropColumn('example_chess_path');
        });
    }
};
