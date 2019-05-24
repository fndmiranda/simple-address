<?php

namespace Fndmiranda\Address;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AddressServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        App::bind('address', Address::class);
    }
}
