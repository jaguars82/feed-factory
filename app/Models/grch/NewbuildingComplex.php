<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewbuildingComplex extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'newbuilding_complex';

    public function developer() {
        return $this->belongsTo(Developer::class);
    }

    public function newbuildings()
    {
        return $this->hasMany(Newbuilding::class);
    }
}
