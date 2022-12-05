<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newbuilding extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'newbuilding';

    public function newbuildinComplex() {
        return $this->belongsTo(NewbuildingComplex::class, 'newbuilding_complex_id');
    }
}
