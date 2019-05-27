<?php

namespace Fndmiranda\Address\Entities;

use Fndmiranda\Address\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address_neighborhoods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'city_id',
    ];

    /**
     * Get the city of the neighborhood.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    /**
     * Get the addresses for the neighborhood.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, 'neighborhood_id', 'id');
    }
}
