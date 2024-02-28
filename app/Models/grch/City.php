<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'city';

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
