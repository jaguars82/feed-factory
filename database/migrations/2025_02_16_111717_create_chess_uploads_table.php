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
        Schema::create('chess_uploads', function (Blueprint $table) {
            $table->id();
            $table->json('chess_idies_list')->comment('Full list of active chessboard IDs at the time of update');
            $table->json('updated_chess_idies')->comment('IDs of successfully updated chessboards');
            $table->json('not_updated_chess_idies')->comment('IDs of chessboards that were not updated');
            $table->json('updated_chess_info')->comment('List of arrays with information about successfully updated chessboards');
            $table->json('not_updated_chess_info')->comment('List of arrays with information about chessboards that were not updated');
            $table->json('all_attachments_by_developer')->comment('List of all chessboard files grouped by developers');
            $table->json('all_attachments_by_developer_and_email')->comment('List of all chessboard files grouped by developers and email');
            $table->json('updated_attachments_by_developer')->comment('List of chessboard files involved in the update, grouped by developers');
            $table->json('updated_attachments_by_developer_and_email')->comment('List of chessboard files involved in the update, grouped by developers and email');
            $table->json('not_updated_attachments_by_developer')->comment('List of chessboard files not involved in the update, grouped by developers');
            $table->json('not_updated_attachments_by_developer_and_email')->comment('List of chessboard files not involved in the update, grouped by developers and email');
            $table->json('updated_developers')->comment('Array of objects with developers (ID and name) whose chessboards were updated');
            $table->json('not_updated_developers')->comment('Array of objects with developers (ID and name) whose chessboards were in the active list but none were updated');
            $table->timestamp('update_session_at')->comment('Date and time of the chessboard update session');
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
        Schema::dropIfExists('chess_uploads');
    }
};
