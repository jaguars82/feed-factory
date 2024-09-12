<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreetType extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'street_type';

}
