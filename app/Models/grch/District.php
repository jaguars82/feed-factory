<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'district';

    public function city()
    {
        return $this->belongsTo(City::class);
    }

}
