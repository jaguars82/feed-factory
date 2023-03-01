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
            $table->foreignId('transport_id')->index()->constrained()->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropForeign('chesses_transport_id_foreign');
            $table->dropIndex('chesses_transport_id_index');
        });
    }
};
