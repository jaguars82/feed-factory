<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChessUpload extends Model
{
    use HasFactory;

    protected $table = 'chess_uploads';

    protected $fillable = [
        'chess_idies_list',
        'updated_chess_idies',
        'not_updated_chess_idies',
        'updated_chess_info',
        'not_updated_chess_info',
        'all_attachments_by_developer',
        'all_attachments_by_developer_and_email',
        'updated_attachments_by_developer',
        'updated_attachments_by_developer_and_email',
        'not_updated_attachments_by_developer',
        'not_updated_attachments_by_developer_and_email',
        'new_attachments_by_developer',
        'removed_attachments_by_developer',
        'previous_sessions_by_developer',
        'updated_developers',
        'not_updated_developers',
        'update_session_at',
    ];

    protected $casts = [
        'chess_idies_list' => 'array',
        'updated_chess_idies' => 'array',
        'not_updated_chess_idies' => 'array',
        'updated_chess_info' => 'array',
        'not_updated_chess_info' => 'array',
        'all_attachments_by_developer' => 'array',
        'all_attachments_by_developer_and_email' => 'array',
        'updated_attachments_by_developer' => 'array',
        'updated_attachments_by_developer_and_email' => 'array',
        'not_updated_attachments_by_developer' => 'array',
        'not_updated_attachments_by_developer_and_email' => 'array',
        'new_attachments_by_developer' => 'array',
        'removed_attachments_by_developer' => 'array',
        'previous_sessions_by_developer' => 'array',
        'updated_developers' => 'array',
        'not_updated_developers' => 'array',
        'update_session_at' => 'datetime',
    ];
}
