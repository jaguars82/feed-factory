<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondaryAdvertisement extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'secondary_advertisement';

    public function secondaryRooms()
    {
        return $this->hasMany(SecondaryRoom::class, 'advertisement_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
