<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondaryRoomImage extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'secondary_room_image';

}
