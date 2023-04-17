<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chess extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'feed_id',
        'complex_feed_name',
        'building_feed_name',
        'developer_alias',
        'complex_alias',
        'building_alias',
        'name',
        'scheme',
        'sheet_name',
        'sheet_index',
        'entrances_data',
        'color_legend',
        'is_active',
        'example_chess_path',
        'file_chess_path',
        'sender_email',
        'attachment_filename',
        'last_completed_formstep',
        'is_configuration_complete',
        'transport_id'
    ];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }
}
