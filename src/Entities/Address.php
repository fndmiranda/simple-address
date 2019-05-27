<?php

namespace Fndmiranda\Address\Entities;

use Fndmiranda\Address\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'postcode', 'neighborhood_id',
    ];

    /**
     * Get the neighborhood of the address.
     */
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id', 'id');
    }

    /**
     * Get all of the owning addressable models.
     */
    public function addressable()
    {
        return $this->morphTo();
    }
}
