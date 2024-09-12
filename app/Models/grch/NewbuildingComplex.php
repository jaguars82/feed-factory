<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NewbuildingComplex extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;

    protected $connection = 'mysql_grch';

    protected $table = 'newbuilding_complex';

    public function developer() {
        return $this->belongsTo(Developer::class);
    }

    public function newbuildings()
    {
        return $this->hasMany(Newbuilding::class);
    }

    public function newbuildingsActive()
    {
        return $this->hasMany(Newbuilding::class)->onlyActive();
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function streetType()
    {
        return $this->belongsTo(StreetType::class);
    }

    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'newbuilding_complex_image', 'newbuilding_complex_id', 'image_id');
    }


    public function addressShort()
    {
        $address = '';

        // street with street type
        if(!empty($this->streetType) && !empty($this->street_name)) {
            $address .= "{$this->streetType->short_name} {$this->street_name}";
        }
        
        // building number with or wihout building type
        if(!empty($this->buildingType) && !empty($this->building_number)) {
            if(!empty($this->street_name)) {
                $address .= ", "; 
            }
            $address .= " {$this->buildingType->short_name} {$this->building_number}";
        } elseif(!empty($this->building_number)) {
            if(!empty($this->street_name)) {
                $address .= ", "; 
            }
            $address .= "{$this->building_number}";
        }

        return $address;
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

    /**
     * Local scope to filter only active Newbuilding Comlexes
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyWithActiveBuildings(Builder $query)
    {
        return $query->where('has_active_buildings', 1);
    }
}
