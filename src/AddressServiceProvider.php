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
        $this->mergeConfigFrom(__DIR__.'/../config/address.php', 'address');

        App::bind('address', Address::class);
    }
}
