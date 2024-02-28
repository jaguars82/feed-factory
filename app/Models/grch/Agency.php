<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'agency';

    public function advertisements()
    {
        return $this->hasMany(SecondaryAdvertisement::class);
    }
}
