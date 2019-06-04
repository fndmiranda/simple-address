# Art of README

*Este artigo foi traduzido do [Inglês](README.md) e traduzido para [Português](README-pt-BR.md).*

## Endereço simples para Laravel

Este pacote simplifica a busca de endereços por CEP em Api's e o gerenciamento de endereços no banco de dados,
você também pode criar seus próprios adaptadores para consultas em uma Api.

### Instalação

```
composer require fndmiranda/simple-address
```

### Uso

Publique o arquivo de configuração do pacote com o comando `vendor: publish` Artisan:

```terminal
php artisan vendor:publish --tag=simple-address-config
```

O arquivo de configuração publicado `address.php` será colocado no diretório` config`.

## Api`s para pesquisa

A lista de Api disponível está localizada no seu arquivo `config/address.php` no` apis` e você pode remover ou 
adicionar novos adaptadores da seguinte maneira:

```php
'apis' => [
    Fndmiranda\SimpleAddress\Adapters\ViaCepAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\PostmonAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\WidenetAdapter::class,
],
```

Se você alterar `force_priority` em` config/address.php` para `true`, a ordem de busca sempre estará de 
acordo com a lista de adaptadores em `apis`, por padrão, esse valor é `false` para que a ordem seja aleatória.

Com o método `search` da facade do` Address`, o pacote executará um loop nas apis até encontrar o endereço 
com o CEP solicitado da seguinte forma:

```php
$address = Address::search(38017170);
```

## Geocodificação

Você pode usar os dados retornados pelo método `search` para obter a `latitude` e a `longitude` do endereço com o
método `geocoding` da facade` Address` da seguinte forma:

```php
$address = Address::search(38017170);

$geocode = Address::geocoding($address);
```

Observação, para usar o recurso `geocoding` você precisa fornecer a `key` da API do Google Maps, 
adicione a entrada `ADDRESS_GOOGLE_MAPS_KEY` no seu arquivo `.env` da seguinte maneira:

```env
ADDRESS_GOOGLE_MAPS_KEY=YourMapsKey
```

## Banco de dados

Este pacote vem com uma estrutura de banco de dados completa para armazenar os endereços pesquisados.

Observe que uma tabela para polimorfismo será criada e que deve ser criada com o tipo de coluna igual ao que fará 
relação com suas tabelas, para isso você deve definir o `column_type` no arquivo` config/address.php`
as opções são `integer`, ` bigInteger` e `uuid`, então crie as tabelas com o comando `migrate` Artisan:

```terminal
php artisan migrate
```

### Personalização das migrações

Se você não for usar as migrações padrão do SimpleAddress, você deve chamar o método `Address::ignoreMigrations` no 
método `register` do seu `AppServiceProvider`.
E então exportar as migrações padrão usando o comando `vendor:publish` Artisan:

```terminal
php artisan vendor:publish --tag=simple-address-migrations
```

Se você não deseja gerenciar os endereços no banco de dados e apenas deseja consultar nas Api's,
mude o arquivo `config/address.php` o `manager_address` para `false`.

### Salvando no banco de dados

Exemplo de integração do modelo de fornecedor com polimorfismo de endereço.

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

Você pode então salvar o endereço para um fornecedor usando os métodos `search` e` geocoding` da facade `Address`
como no exemplo a seguir:

```php
// Encontre um fornecedor
$supplier = \App\Supplier::find(1);

// Pesquise um endereço por código postal
$address = Address::search(38017170);

// Obter geocódigo do endereço
$geocode = Address::geocoding($address);

// Salvar um endereço para o fornecedor
$attributes = array_merge(['number' => 16, 'complement' => 'House'], $geocode);
$supplier->addresses()->save($address, $attributes);

// Ou sem geocódigo
$supplier->addresses()->save($address, ['number' => 16, 'complement' => 'House']);

// Para atualizar um endereço de fornecedor
$supplier->addresses()->first()->pivot->update(['number' => 25, 'complement' => 'Store 10']);
```

### Ou no seu controlador

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

Exemplo do corpo da solicitação

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

## Criando seu adaptador customizado

Você pode criar seu próprio adaptador personalizado para consultar uma API que não está na lista.

Você pode gerar um adaptador do comando `simple-address:make` Artisan:

```terminal
php artisan simple-address:make YourApiAdapter
```

Este comando gerará um adaptador por padrão em `app/SimpleAddress/Adapters/YourApiAdapter.php`.

O arquivo irá conter os métodos `search` e `prepare` vazios, para que você possa adaptá-los seguindo o arquivo
de exemplo a seguir:

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

Adicione seu adaptador à lista `apis` no arquivo `config/address.php` da seguinte forma:

```php
'apis' => [
    Fndmiranda\SimpleAddress\Adapters\ViaCepAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\PostmonAdapter::class,
    Fndmiranda\SimpleAddress\Adapters\WidenetAdapter::class,
    App\SimpleAddress\Adapters\YourApiAdapter::class, // Seu adaptador de Api personalizado
],
```

Se você criar um novo adaptador Api, eu agradeceria se você abrir um pull request adicionando seu adaptador e o mapeando
na lista `apis` em `config/address.php` do pacote.

### Método search

O método `search` envia uma request para um endpoint para consultar um `postcode` e usa o método `prepare` para transformar os
dados obtidos em um array padrão e os retorna ou retorna `false` se o `postcode` não for encontrado ou se a API 
não estiver respondendo para que ele consulte automaticamente a próximo api `adapter` até encontrar o endereço pelo CEP fornecido.

### Método prepare

O método `prepare` transformará os dados retornados por uma API em um `array` padrão com as
chaves `postcode`, `address`, `neighborhood`, `city` e `state`.

## Segurança

Se você descobrir algum problema relacionado à segurança, envie um e-mail para fndmiranda@gmail.com em vez de usar o 
rastreador de problemas.

## Licença

A licença MIT (MIT). Por favor, veja o [Arquivo de licença](LICENSE.md) para mais informações.
