# Laracogs

**Laracogs** - A handful of tools for Laravel

[![Codeship](https://img.shields.io/codeship/013b03f0-a7c6-0133-63e0-5a0bf9327500.svg)](https://github.com/YABhq/Laracogs)

This is a set of tools to help speed up development of Laravel apps. You can start an app with various parts prewritten (Users, User Meta, Roles, Teams). And it comes with a powerful FormMaker which can generate form content from tables, and objects. It can generate epic CRUD prototypes rapidly with full testing scripts prepared for you, requiring very little editing. It also provides an elegant Cryptography tool which is URL friendly. Finally it brings along some friends with the LaravelCollective as a vendor.

## Website
[http://laracogs.com](http://laracogs.com)

**Author(s):**
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), matt at yabhq dot com)
* [Chris Blackwell](https://github.com/chrisblackwell) ([@chrisblackwell](https://twitter.com/chrisblackwell), chris at yabhq dot com)

## Requirements

1. PHP 5.5.9+
2. OpenSSL
3. Laravel 5.1+

### Composer
Start a new Laravel project:
```php
composer create-project laravel/laravel your-project-name
```

Then run the following to add Laracogs
```php
composer require "yab/laracogs"
```

### Providers
Add this to the `config/app.php` in the providers array:
```php
Yab\Laracogs\LaracogsProvider::class
```

### Publish
```php
php artisan vendor:publish --provider="Yab\Laracogs\LaracogsProvider"
```

----

## Documentation
Please consult the documentation here: [http://laracogs.com/docs](http://laracogs.com/docs)

## Commands
The console commands provided by Laracogs are as follows:

### Starter
----
Once you've added in all these parts you may want to run the starter for your application!

```php
php artisan laracogs:starter
```

##### Migrate
You will need to migrate to add in the users, user meta, roles and teams tables. The seeding is run to set the initial roles for your application.

```php
php artisan migrate --seed
```

##### Route Middleware
Add the following to your `app/Http/Kernel.php` $routeMiddleware array.

```php
'admin' => \App\Http\Middleware\Admin::class,
```

##### User Model
Update the model in: 'config/auth.php' and 'database/factory/ModelFactory.php' it should look similar to:

```php
App\Repositories\User\User::class
```

##### AuthServiceProvider
Add the following to 'app/Providers/AuthServiceProvider.php' in the boot method

```php
$gate->define('admin', function ($user) {
    return ($user->roles->first()->name === 'admin');
});

$gate->define('team-member', function ($user, $team) {
    return ($user->teams->find($team->id));
});
```

##### Testing
You will want to create an sqlite memory test DB

```php
'testing' => [
    'driver'   => 'sqlite',
    'database' => ':memory:',
    'prefix'   => '',
],
```

Add the following line to the 'phpunit.xml' file
```xml
<env name="DB_CONNECTION" value="testing"/>
<env name="MAIL_DRIVER" value="log"/>
```

##### For Laravel 5.2
You will also need to set the location of the email for password reminders. (app/auth.php - at the bottom)

```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'email' => 'emails.password',
        'table' => 'password_resets',
        'expire' => 60,
    ],
],
```

##### Things to note
- You may try and start quickly by testing the registration - make sure your app's
**email** is configured or it will throw an exception.

You can do this in the .env file easily by setting it to 'log' temporarily

```php
MAIL_DRIVER=log
```

### Boostrap
----
Boostrap prepares your application with bootstrap as a view/ css framework

```php
php artisan laracogs:bootstrap
```

### Semantic
----
Semantic prepares your application with semantic-ui as a view/ css framework

```php
php artisan laracogs:semantic
```

<small>Please note that Billing and Notifications are still set for bootstrap</small>

### Notifications
----
Notifications prepares your application with a notification system

```php
php artisan laracogs:notifications
```

### Socialite
----
Socialite prepares your application with a socialite system, with GitHub as the example:

```php
php artisan laracogs:socialite
```

### API
----
Api prepares your application with an API system using JWT (logins, and user profile):

```php
php artisan laracogs:api
```

### Billing
----
The billing command sets up your app with Laravel's cashier - it prepares the whole app to handle subscriptions with a policy structure.
```php
php artisan laracogs:billing
```

##### Requires
```php
composer require laravel/cashier
```

You may want to add this line to your navigation:
```php
<li><a href="{!! url('user/billing/details') !!}"><span class="fa fa-dollar"></span> Billing</a></li>
```

This to the app/Providers/ReouteServiceProvider.php:
```php
require app_path('Http/billing-routes.php');
```

This to the .env:
```php
SUBSCRIPTION=basic
STRIPE_SECRET=public-key
STRIPE_PUBLIC=secret-key
```

This to the app/Providers/RouteServiceProvider.php:
```php
$gate->define('access-billing', function ($user) {
    return ($user->meta->subscribed('main') && is_null($user->meta->subscription('main')->endDate));
});
```

For the `config/services.php` you will want yours to resemble:
```php
'stripe' => [
    'model'  => App\Repositories\UserMeta\UserMeta::class,
    'key'    => env('STRIPE_PUBLIC'),
    'secret' => env('STRIPE_SECRET'),
],
```

Finally run migrate to add the subscrptions and bind them to the user meta:
```php
php artisan migrate
```

You will also want to update your gulpfile.js to include the card.js, and subscription.js
```js
elixir(function(mix) {
    mix.scripts([
        'app.js',
        'card.js',
        'subscription.js'
    ]);
});
```

#### Accounts (Billing Only)

##### Plans
This is the basic config for `config/plans.php`. It sets the default subscription name, as well as the plans and the rules pertaining to them.

```
'subscription_name' => 'main',
'plans' => [
    'basic' => [
        'access' => [
            'some name'
        ],
        'limits' => [
            'Model\Namespace' => 5,
            'pivot_table' => 1
        ],
        'credits' => [
            'column' => 'credits_spent',
            'limit' => 10
        ],
        'custom' => [
            'anything' => 'anything'
        ],
    ],
]
```

##### Service
The service provides extra tools for handling restrictions in your application based on the plan the user subscribed to.
```php
getClause('box_limit');
canAccess('area_51');
cannotAccess('restricted_area');
getLimit('team_user');
withinLimit('App\Repositories\Team\Team');
creditsAvailable('App\Repositories\Team\Team');
creditsUsed('App\Repositories\Team\Team');
currentBillingCycle()->withinLimit('App\Repositories\Team\Team');
clause('custom', function($user, $subscription, $clause, $query) {
    // do your own logic!
    // model is optional if you dont provide it the query is null - otherwise is a query builder
}, 'App\Repositories\Team\Team');
```

## CRUD
The CRUD command builds a crud for a table with unit tests!
```php
php artisan laracogs:crud {table} {--migration} {--bootstrap} {--semantic} {--schema}
```

## Docs
The docs can prepare documentation for buisness rules or prepare your app for API doc generation with Sami.
```php
php artisan laracogs:crud {action} {name=null} {version=null}
```

## Facades/ Utilities
Laracogs provides a handful of easy to use tools outside of the app starter kit, and CRUD builder:

#### Crypto
```php
Crypto::uuid();
Crypto::encrypt('string');
Crypto::decrypt('enc-string');
Crypto::shared()->encrypt('string');
Crypto::shared()->decrypt('enc-string');
```

#### FormMaker
```php
FormMaker::fromTable($table, $columns = null, $class = 'form-control', $view = null, $reformatted = true, $populated = false, $idAndTimestamps = false)
FormMaker::fromObject($object, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
FormMaker::fromArray($array, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
```

#### InputMaker
```php
InputMaker::label($name, $attributes = [])
InputMaker::create($name, $field, $object = null, $class = 'form-control', $reformatted = false, $populated = false)
```

## License
Laracogs is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
