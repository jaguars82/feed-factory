<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Flat extends Model
{
    use HasFactory;

    const STATUS_SALE = 0;
    const STATUS_RESERVED = 1;
    const STATUS_SOLD = 2;

    protected $connection = 'mysql_grch';

    protected $table = 'flat';

    public function newbuilding() {
        return $this->belongsTo(Newbuilding::class, 'newbuilding_id');
    }

    /**
     * Local scope to filter only active Newbuilding Comlexes
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlySale(Builder $query)
    {
        return $query->where('status', self::STATUS_SALE);
    }
}
