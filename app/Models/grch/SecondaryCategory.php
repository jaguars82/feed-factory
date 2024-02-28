<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondaryCategory extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'secondary_category';

    public function secondaryRooms()
    {
        return $this->hasMany(SecondaryRoom::class, 'category_id');
    }
}
