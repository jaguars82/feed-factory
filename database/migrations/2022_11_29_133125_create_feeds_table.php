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
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->index()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
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
        schema::table('feeds', function (Blueprint $table) {
            $table->dropForeign('feeds_provider_id_foreign');
            $table->dropIndex('feeds_provider_id_index');
        });

        Schema::dropIfExists('feeds');
    }
};
