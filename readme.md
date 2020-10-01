# Inspired Deck interface for Laravel

The Inspired Deck Interface for Laravel gives you an API interface into the Inspired Deck Platform
for your Laravel applications.

## Integration Applications

Inspired Deck provides an interface for the integrator to manage their own set of security credentials. This interface is 
available from the ‘Integrations’ menu item of the Inspired Deck Portal.
 
 - [Live Integration Applications](https://portal.inspireddeck.co.uk/integration) / [Staging Integration Applications](https://staging-portal.inspireddeck.co.uk/integration)
 - [Live Documentation](https://portal.inspireddeck.co.uk/docs) / [Staging Documentation](https://staging-portal.inspireddeck.co.uk/docs)

## Installation

The recommended way to install Inspired Deck Larvel is through [Composer](https://getcomposer.org/).

```bash
composer require mblsolutions/inspireddeck-php-laravel
```

## Laravel without auto-discovery

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php

```php
\MBLSolutions\InspiredDeckLaravel\InspiredDeckServiceProvider::class,
```

If you want to use the facade for authentication, add this to your facades in app.php:

```php
'InspiredDeckAuth' => \MBLSolutions\InspiredDeckLaravel\InspiredDeckAuthFacade::class,
```

## Usage

Copy the package config to your local config with the publish command:

```bash
php artisan vendor:publish --provider="MBLSolutions\InspiredDeckLaravel\InspiredDeckServiceProvider"
```

A new config file will be available in `config/inspireddeck.php`, please familiarise yourself with the available 
environment variables that should be setup in `.env`.

```dotenv
ID_ENDPOINT=https://inspireddeck.co.uk
ID_CLIENT_ID=1
ID_SECRET=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy
```

### Authentication

To authenticate to Inspired Deck use the login method on the Authentication object.

```php
$deckAuthentication = new \MBLSolutions\InspiredDeckLaravel\Authentication;

$deckAuthentication->login('john.doe@example.com', 'password');
```

Once authenticated, the authentication will be stored in the session key specified in the inspireddeck config.

### Checking authentication/Issuing Transactions

```php
$deckAuthentication = new \MBLSolutions\InspiredDeckLaravel\Authentication;

if (!$deckAuthentication->isAuthenticated()) {
    $deckAuthentication->login('john.doe@example.com', 'password');
}

// Perform transaction
$balance = new \MBLSolutions\InspiredDeck\Balance;

$result = $balance->balance([
    'serial' => 123456789
]);
```

### Further documentation

See the Inspired Deck Interface for PHP [package](https://github.com/mblsolutions/inspireddeck-php) for further documentation on issuing transactions.

### License

Inspired Deck Interface for Laravel is free software distributed under the terms of the MIT license.

A contract/subscription to Inspired Deck is required to make use of this package, for more information contact 
[MBL Solutions](mailto:support@mblsolutions.co.uk)