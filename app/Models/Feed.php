<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'name',
        'is_active'
    ];

    public function chesses() {
        return $this->hasMany(Chess::class);
    }
}
