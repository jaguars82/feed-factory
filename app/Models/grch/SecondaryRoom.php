<?php

namespace App\Models\grch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondaryRoom extends Model
{
    use HasFactory;

    protected $connection = 'mysql_grch';

    protected $table = 'secondary_room';

    public function images()
    {
        return $this->hasMany(SecondaryRoomImage::class);
    }

    public function advertisement()
    {
        return $this->belongsTo(SecondaryAdvertisement::class, 'advertisement_id');
    }

    public function category()
    {
        return $this->belongsTo(SecondaryCategory::class, 'category_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function regionDistrict()
    {
        return $this->belongsTo(RegionDistrict::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
