<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'region';

    public function regionDistricts()
    {
        return $this->hasMany(RegionDistrict::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
