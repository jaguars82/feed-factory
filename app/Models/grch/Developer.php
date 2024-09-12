<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'developer';

    public function newbuildingComplexes()
    {
        return $this->hasMany(NewbuildingComplex::class);
    }

    public function newbuildingComplexesWithActiveBuildings()
    {
        return $this->hasMany(NewbuildingComplex::class)->onlyActive()->onlyWithActiveBuildings();
    }
}
