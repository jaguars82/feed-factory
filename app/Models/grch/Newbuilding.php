<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Newbuilding extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;

    protected $connection = 'mysql_grch';

    protected $table = 'newbuilding';

    public function newbuildinComplex() {
        return $this->belongsTo(NewbuildingComplex::class, 'newbuilding_complex_id');
    }

    public function flats()
    {
        return $this->hasMany(Flat::class);
    }

    public function flatsSale()
    {
        return $this->hasMany(Flat::class)->onlySale();
    }

    /**
     * Local scope to filter only active Newbuilding Comlexes
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyActive(Builder $query)
    {
        return $query->where('active', self::STATUS_ACTIVE);
    }

}
