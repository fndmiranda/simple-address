<?php

namespace Fndmiranda\Repositories;

use Fndmiranda\Address\Entities\State;

class StateRepository
{
    /**
     * Retrieve state by name, or create it.
     *
     * @param string $name
     * @return \Fndmiranda\Address\Entities\State
     */
    public function firstOrCreate($name)
    {
        return State::firstOrCreate(['name' => $name]);
    }
}
