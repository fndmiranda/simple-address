# Simple address from Laravel

This package simplifies the search for addresses by zip code in Api`s and the management of addresses in the database, 
you can also create your own adapters for api queries.

## Installation

```
composer require fndmiranda/simple-address
```

## Usage

Publish the package configuration file with `vendor:publish` Artisan command:

```terminal
php artisan vendor:publish --tag=simple-address-config
```

The published configuration file `address.php` will be placed in your `config` directory.

### Api`s of search

The list of Api's available is located in your `config/address.php` file in `apis` and you can remove or add new adapters as follows:

```php
'apis' => [
    Fndmiranda\SimpleAddress\Adapters\ViaCepAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\PostmonAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\WidenetAdapter::class,
],
```

If you change `force_priority` in `config/address.php` to `true` the search order will always conform to the list of `apis` 
adapters, by default this value is `false` for the order to be random.

With the `search` method on the facade of the `Address` the package will loop in the apis until finding the requested postcode as follows:

```php
$address = Address::search(38017170);
```

### Geocoding

You can use the data returned by the `search` method to obtain the `latitude` and `longitude` of the address with the 
`geocoding` method of the facade `Address` as follows:

```php
$address = Address::search(38017170);

$geocode = Address::geocoding($address);
```

Note To use the `geocoding` feature you need to provide the Google Maps API `key`, add the `ADDRESS_GOOGLE_MAPS_KEY` 
entry in your `.env` file as follows:

```env
ADDRESS_GOOGLE_MAPS_KEY=YourMapsKey
```

### Database

This package comes with a complete database structure to store the searched addresses.

Note that a table for polymorphism will be created, which should be created with the type of column that will make the 
relation the same that you use in your tables by setting the `column_type` in the `config/address.php` file and the 
options are `integer`, `bigInteger` and `uuid` there then create the tables with `migrate` Artisan command:

```terminal
php artisan migrate
```

### Migration Customization

If you are not going to use SimpleAddress default migrations, you should call the `Address::ignoreMigrations` method in 
the register method of your `AppServiceProvider`. 
You may export the default migrations using `vendor:publish` Artisan command:

```terminal
php artisan vendor:publish --tag=simple-address-migrations
```

If you do not want to manage the addresses in the database and just want to query in api, 
change the `config/address.php` file `manager_address` to `false`.

### Saving in database

Example of integration of supplier model with address polymorphism.

```php
<?php

namespace App\Supplier;

use Fndmiranda\SimpleAddress\Entities\Address;
use Illuminate\Database\Eloquent\Model;
use Fndmiranda\SimpleAddress\Pivot\AddressPivot;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'document', 'is_active',
    ];

    /**
     * Get all of the addresses for the supplier.
     */
    public function addresses()
    {
        return $this->morphToMany(Address::class, 'addressable', 'address_addressables')
            ->withPivot(['number', 'complement', 'lat', 'lng', 'type'])
            ->using(AddressPivot::class)
            ->withTimestamps();
    }
}
```

You can then save the address to a supplier by using the `search` and `geocoding` methods of the facade `Address` 
as in the following example:

```php
// Find a supplier
$supplier = \App\Supplier::find(1);

// Search a address by postcode
$address = Address::search(38017170);

// Get geocode of address
$geocode = Address::geocoding($address);

// Save an address to the supplier
$attributes = array_merge(['number' => 16, 'complement' => 'House'], $geocode);
$supplier->addresses()->save($address, $attributes);

// Or without geocode
$supplier->addresses()->save($address, ['number' => 16, 'complement' => 'House']);

// To update a supplier address
$supplier->addresses()->first()->pivot->update(['number' => 25, 'complement' => 'Store 10']);
```

#### In your controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Supplier;
use App\Http\Resources\SupplierResource;
use App\Repositories\SupplierRepository;
use Fndmiranda\SimpleAddress\Facades\Address;
use Fndmiranda\SimpleAddress\Repositories\AddressRepository;

class SupplierController extends Controller
{
    /**
     * The supplier repository instance.
     *
     * @var SupplierRepository
     */
    protected $supplierRepository;
    
    /**
     * The address repository instance.
     *
     * @var AddressRepository
     */
    protected $addressRepository;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SupplierRepository $supplierRepository, AddressRepository $addressRepository)
    {
        $this->supplierRepository = $supplierRepository;
        $this->addressRepository = $addressRepository;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param SupplierRequest $request
     * @return Response
     */
    public function store(SupplierRequest $request)
    {
        $attributes = $request->all();
        
        $entity = $this->addressRepository->create($attributes);
        
        if (!empty($attributes['address'])) {
            if (!empty(config('address.google_maps_key'))) {
                $address = $this->addressRepository->find($attributes['address']['address_id']);
                $geocode = Address::geocoding($address, $attributes['address']);
                if (!empty($geocode)) {
                    array_merge($attributes['address'], $geocode);
                }
            }

            $entity->addresses()->sync([$attributes['address']['address_id'] => $attributes['address']]);
        }
        
        $data = SupplierResource::make($entity);
        return response()->json(['data' => $data], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SupplierRequest $request
     * @param string $id
     * @return AccountResource
     */
    public function update(SupplierRequest $request, $id)
    {
        $attributes = $request->all();
        
        $entity = $this->supplierRepository->find($id);
        
        if (!empty($attributes['address'])) {
            if (!empty(config('address.google_maps_key'))) {
                $address = $this->addressRepository->find($attributes['address']['address_id']);
                $geocode = Address::geocoding($address, $attributes['address']);
                if (!empty($geocode)) {
                    array_merge($attributes['address'], $geocode);
                }
            }

            $entity->addresses()->sync([$attributes['address']['address_id'] => $attributes['address']]);
        }

        $entity->update($attributes);
        
        return SupplierResource::make($entity);
    }
}
```

Request body example

```json
{
  "name": "Name of supplier",
  "email": "email@domain.com",
  "document": "11111111111",
  "is_active": true,
  "address": {
  	"address_id": "0fdecea5-9f99-47ea-87a9-3dc191839008",
  	"number": 16,
  	"complement": "House"
  }
}
```

## Creating your custom adapter

You can create your own custom adapter to query an API that is not in the list, you may generate an 
adapter of the `simple-address:make` Artisan command:

```terminal
php artisan simple-address:make YourApiAdapter
```

This command will generate a adapter at `app/SimpleAddress/Adapters/YourApiAdapter.php`. 

The file will contain the empty `search` and` prepare` methods, so you can adapt them by following the file 
structure as in the following example:

```php
<?php

namespace App\SimpleAddress\Adapters;

use Fndmiranda\SimpleAddress\Contracts\AdapterContract;

class YourApiAdapter implements AdapterContract
{
    /**
     * Search external address by postcode.
     *
     * @param $postcode
     * @return array|bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($postcode)
    {
        $client = new \GuzzleHttp\Client();
        $request = new \GuzzleHttp\Psr7\Request('GET', 'https://api.postmon.com.br/v1/cep/'.$postcode.'?format=json');
        $response = $client->send($request);

        if ($response->getStatusCode() != 200) {
            return false;
        }

        $data = json_decode((string) $response->getBody(), true);

        return $this->prepare($data);
    }

    /**
     * Prepare address data.
     *
     * @param $data
     * @return array
     */
    public function prepare($data)
    {
        return [
            'postcode' => $data['cep'],
            'address' => $data['logradouro'],
            'neighborhood' => $data['bairro'],
            'city' => $data['cidade'],
            'state' => $data['estado'],
        ];
    }
}
```

Add your adapter to the `apis` list in the `config/address.php` file as follows:

```php
'apis' => [
    Fndmiranda\SimpleAddress\Adapters\ViaCepAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\PostmonAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\WidenetAdapter::class,
    App\SimpleAddress\Adapters\YourApiAdapter::class, // Your custom Api adapter
],
```

If you create a new Api adapter, I would appreciate if you open a pull request by adding your adapter and mapping 
it in the `apis` list in `config/address.php` of the package.

#### Method search

The `search` method sends the request to an endpoint to query a `postcode` and uses the `prepare` method to transform the 
obtained data into a standard array and returns them or returns `false` if the `postcode` is not found or if api does 
not respond so that it automatically query on the next api `adapter`.

#### Method prepare

The `prepare` method will transform the data returned by an api into a standard `array` with the 
keys `postcode`, `address`, `neighborhood`, `city` and `state`.

## Security

If you discover any security related issues, please email fndmiranda@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
