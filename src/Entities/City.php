<?php

namespace Fndmiranda\SimpleAddress\Entities;

use Fndmiranda\SimpleAddress\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address_cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'state_id',
    ];

    /**
     * Get the state of the city.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    /**
     * Get the neighborhoods for the city.
     */
    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class, 'city_id', 'id');
    }
}
