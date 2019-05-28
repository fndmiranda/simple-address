# Simple address from Laravel

This package simplifies the search and management of addresses, being able to create and add new adapter of search 
in public apis by postcode.

## Installation

```
composer require fndmiranda/simple-address
```

## Usage

You may generate an data migration of the `data-migration:make` Artisan command:

```terminal
php artisan data-migration:make PermissionDataMigration
```

This command will generate a data migration at `app/DataMigrations/PermissionDataMigration.php`. The data migration will contain the `model`, `data`, and `options` methods.

```php
<?php

namespace App\DataMigrations;

use Fndmiranda\DataMigration\Contracts\DataMigration;

class PermissionDataMigration implements DataMigration
{
    /**
     * Get the model being used by the data migration.
     *
     * @return string
     */
    public function model()
    {
        //
    }

    /**
     * Get the data being used by the data migration.
     *
     * @return mixed
     */
    public function data()
    {
        //
    }

    /**
     * Get the data options being used by the data migration.
     *
     * @return mixed
     */
    public function options()
    {
        //
    }
}
```

#### Method model

Method to specify the model bound to the data migration class.

```php
/**
 * Get the model being used by the data migration.
 *
 * @return string
 */
public function model()
{
    return \App\Permission::class;
}
```

#### Method data

Method to specify the data to be migrated.

```php
/**
 * Get the data being used by the data migration.
 *
 * @return mixed
 */
public function data()
{
    return [
       ['name' => 'product.products.index', 'title' => 'List products', 'group' => 'Product'],
       ['name' => 'product.products.show', 'title' => 'Show product', 'group' => 'Product'],
       ['name' => 'product.products.store', 'title' => 'Create product', 'group' => 'Product'],
       ['name' => 'product.products.update', 'title' => 'Update product', 'group' => 'Product'],
       ['name' => 'product.products.destroy', 'title' => 'Delete product', 'group' => 'Product'],

       ['name' => 'product.brands.index', 'title' => 'List brands', 'group' => 'Product'],
       ['name' => 'product.brands.show', 'title' => 'Show brand', 'group' => 'Product'],
       ['name' => 'product.brands.store', 'title' => 'Create brand', 'group' => 'Product'],
       ['name' => 'product.brands.update', 'title' => 'Update brand', 'group' => 'Product'],
       ['name' => 'product.brands.destroy', 'title' => 'Delete brand', 'group' => 'Product'],
   ];
}
```

#### Method options

The options method to specify the parameters to be used in the migration.

```php
/**
 * Get the data options being used by the data migration.
 *
 * @return mixed
 */
public function options()
{
    return [
       'identifier' => 'name',
       'show' => ['name', 'title'],
   ];
}
```

The following keys are available as options:

Key | Description | Type
--- | --- | ---
identifier | Column with unique value to validate status. | string
show | Columns to show in commands output. | array
relations | Relationships options, see the usage with relationships. | array

## Run a data migration

You can run a data migration via command or facade.

Show the status of each data with the database with `data-migration:status` Artisan command:

```terminal
php artisan data-migration:status App\\DataMigrations\\PermissionDataMigration
```

Output:

```terminal
+--------------------------+------------------------+--------+
| name                     | title                  | status |
+--------------------------+------------------------+--------+
| product.products.index   | List products          | Create |
| product.products.show    | Show product           | OK     |
| product.products.store   | Create product updated | Update |
| product.products.destroy | Delete product         | OK     |
| product.brands.show      | Show brand             | Create |
| product.brands.store     | Create brand updated   | Update |
| product.brands.update    | Update brand           | OK     |
| product.brands.destroy   | Delete brand           | OK     |
| product.products.update  | Update product         | Delete |
| product.brands.index     | List brands            | Delete |
+--------------------------+------------------------+--------+
```

Or with `DataMigration` facade:

```php
$status = DataMigration::status(\App\DataMigrations\PermissionDataMigration::class);
```

Show changes between data migration and database with `data-migration:diff` Artisan command:

```terminal
php artisan data-migration:diff App\\DataMigrations\\PermissionDataMigration
```

Output:

```terminal
+--------------------------+------------------------+--------+
| name                     | title                  | status |
+--------------------------+------------------------+--------+
| product.products.index   | List products          | Create |
| product.products.store   | Create product updated | Update |
| product.brands.show      | Show brand             | Create |
| product.brands.store     | Create brand updated   | Update |
| product.products.update  | Update product         | Delete |
| product.brands.index     | List brands            | Delete |
+--------------------------+------------------------+--------+
```

Or with `DataMigration` facade:

```php
$diff = DataMigration::diff(\App\DataMigrations\PermissionDataMigration::class);
```

Migrate data from a data migration to the database. Only necessary operations with status to create will be executed 
with `data-migration:migrate` Artisan command:

```terminal
php artisan data-migration:migrate App\\DataMigrations\\PermissionDataMigration
```

Output:

```terminal
+--------------------------+------------------------+--------+
| name                     | title                  | status |
+--------------------------+------------------------+--------+
| product.products.index   | List products          | Create |
| product.brands.show      | Show brand             | Create |
+--------------------------+------------------------+--------+
```

Or with `DataMigration` facade:

```php
$migrated = DataMigration::migrate(\App\DataMigrations\PermissionDataMigration::class);
```

Synchronize data from a data migration with the database. All necessary `create`, `update`, and `delete` operations will be 
performed with `data-migration:sync` Artisan command:

```terminal
php artisan data-migration:sync App\\DataMigrations\\PermissionDataMigration
```

Output:

```terminal
+--------------------------+------------------------+--------+
| name                     | title                  | status |
+--------------------------+------------------------+--------+
| product.products.index   | List products          | Create |
| product.products.store   | Create product updated | Update |
| product.brands.show      | Show brand             | Create |
| product.brands.store     | Create brand updated   | Update |
| product.products.update  | Update product         | Delete |
| product.brands.index     | List brands            | Delete |
+--------------------------+------------------------+--------+
```

Or with `DataMigration` facade:

```php
$synchronized = DataMigration::sync(\App\DataMigrations\PermissionDataMigration::class);
```

## Usage with relationships

Example of a permissions model with a relationship for dependencies of type belongsToMany with pivot_example_1 and 
pivot_example_2, and a relationship for brand of type belongsTo to exemplify a data migration.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'title', 'group', 'brand_id',
    ];

    /**
     * The dependencies that belong to the permission.
     */
    public function dependencies()
    {
        return $this->belongsToMany(Permission::class)->withPivot(['pivot_example_1', 'pivot_example_2']);
    }

    /**
     * Get the brand of the permission.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
```

#### Method data with relationships

The data method to specify the data to be migrated with relationships.

```php
/**
 * Get the data being used by the data migration.
 *
 * @return mixed
 */
public function data()
{
    return [
       ['name' => 'product.products.index', 'title' => 'List products', 'group' => 'Product', 'brand' => ['name' => 'Brand test 1']],
       ['name' => 'product.products.show', 'title' => 'Show product', 'group' => 'Product'],
       ['name' => 'product.products.store', 'title' => 'Create product', 'group' => 'Product', 'dependencies' => [
           ['name' => 'product.brands.index', 'pivot_example_1' => 'Pivot value 1'], ['name' => 'product.categories.index'],
       ], 'brand' => ['name' => 'Brand test 2']],
       ['name' => 'product.products.update', 'title' => 'Update product', 'group' => 'Product', 'dependencies' => [
           ['name' => 'product.brands.index'], ['name' => 'product.categories.index', 'pivot_example_2' => 'Pivot value 2'],
       ]],
       ['name' => 'product.products.destroy', 'title' => 'Delete product', 'group' => 'Product'],

       ['name' => 'product.brands.index', 'title' => 'List brands', 'group' => 'Product', 'brand' => ['name' => 'Brand test 1']],
       ['name' => 'product.brands.show', 'title' => 'Show brand', 'group' => 'Product'],
       ['name' => 'product.brands.store', 'title' => 'Create brand', 'group' => 'Product'],
       ['name' => 'product.brands.update', 'title' => 'Update brand', 'group' => 'Product', 'brand' => ['name' => 'Brand test 2']],
       ['name' => 'product.brands.destroy', 'title' => 'Delete brand', 'group' => 'Product'],
   ];
}
```

#### Method options with relationships

The options method with relationships to specify the parameters to be used in the data migration.

```php
/**
 * Get the data options being used by the data migration.
 *
 * @return mixed
 */
public function options()
{
    return [
       'identifier' => 'name',
       'show' => ['name', 'title'],
       'relations' => [
           [
               'type' => 'belongsToMany',
               'relation' => 'dependencies',
               'identifier' => 'name',
               'show' => ['name'],
           ],
           [
               'type' => 'belongsTo',
               'relation' => 'brand',
               'identifier' => 'name',
               'show' => ['name'],
           ],
       ],
   ];
}
```

The following keys are available as relationships options:

Key | Description | Type
--- | --- | ---
relation | Name of the relationship of the model. | string
type | Model relationship type, `belongsToMany` or `belongsTo`. | string
identifier | Column with unique value to validate status. | string
show | Columns to show in commands output. | array

## Security

If you discover any security related issues, please email fndmiranda@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.