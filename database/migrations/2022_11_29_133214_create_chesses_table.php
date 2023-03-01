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
        Schema::create('chesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_id')->index()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('provider_id')->index()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('complex_feed_name'); // name of a newbuilding complex (as it declared in feed)
            $table->string('building_feed_name'); // name of a position/newbuilding (as it declared in feed)
            $table->string('name'); // name of the chess
            $table->string('scheme');
            $table->json('entrances_data'); // for json with entrances params
            $table->boolean('is_active')->default(1);
            $table->timestamps();
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
            $table->dropForeign('chesses_feed_id_foreign');
            $table->dropIndex('chesses_feed_id_index');
            $table->dropForeign('chesses_provider_id_foreign');
            $table->dropIndex('chesses_provider_id_index');
        });

        Schema::dropIfExists('chesses');
    }
};
