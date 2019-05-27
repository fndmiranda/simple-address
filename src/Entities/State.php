<?php

namespace Fndmiranda\Address\Entities;

use Fndmiranda\Address\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address_states';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the cities for the state.
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'state_id', 'id');
    }
}
