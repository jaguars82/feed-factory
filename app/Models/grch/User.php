<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'user';

    public static function getUserById($id)
    {
        return self::find($id);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }
}
