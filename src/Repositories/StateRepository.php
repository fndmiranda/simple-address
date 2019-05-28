<?php

namespace Fndmiranda\SimpleAddress\Repositories;

use Fndmiranda\SimpleAddress\Entities\State;

class StateRepository
{
    /**
     * Retrieve state by name, or create it.
     *
     * @param string $name
     * @return \Fndmiranda\SimpleAddress\Entities\State
     */
    public function firstOrCreate($name)
    {
        return State::firstOrCreate(['name' => $name]);
    }
}
