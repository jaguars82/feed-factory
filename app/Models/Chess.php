<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chess extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function feed() {
        return $this->belongsTo(Feed::class);
    }

    public function provider() {
        return $this->belongsTo(Provider::class);
    }

    public function transport() {
        return $this->belongsTo(Transport::class);
    }
}
