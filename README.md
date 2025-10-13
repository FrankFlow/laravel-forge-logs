![Cover](./assets/cover.jpg)


# Laravel Forge Logs


[![Latest Version on Packagist](https://img.shields.io/packagist/v/frankflow/laravel-forge-logs.svg?style=flat-square)](https://packagist.org/packages/frankflow/laravel-forge-logs)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/frankflow/laravel-forge-logs/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/frankflow/laravel-forge-logs/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/frankflow/laravel-forge-logs/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/frankflow/laravel-forge-logs/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/frankflow/laravel-forge-logs.svg?style=flat-square)](https://packagist.org/packages/frankflow/laravel-forge-logs)

A Laravel package that integrates with the [Laravel Forge API](https://forge.laravel.com/docs/api-reference/introduction) to fetch and manage application logs (laravel and nginx) from your Forge-managed sites. Simplify log retrieval and monitoring with interactive CLI commands.

## Why this package
With the use of AI, it is increasingly necessary to access logs remotely to speed up bug fixing.

## Features

- Interactive setup wizard to configure your Forge organization, server, and site
- Fetch application logs directly from Laravel Forge
- Fetch nginx access logs from Laravel Forge
- Fetch nginx error logs from Laravel Forge
- Customizable log file paths and filenames
- Store logs locally for analysis
- Simple artisan commands for easy integration 

## Requirements

- PHP 8.4+
- Laravel 11.x or 12.x

## Installation

You can install the package via Composer:

```bash
composer require frankflow/laravel-forge-logs --dev
```
 
## Get Your Laravel Forge API Token

First, obtain your API token from Laravel Forge:

1. Log in to [Laravel Forge](https://forge.laravel.com)
2. Go to your account settings
3. Navigate to the API section 
4. Generate a new API token (make sure you set the right scopes for the token)

## Add Your Forge Token to .env

Add the following line to your `.env` file:

```env
FORGE_TOKEN=your-forge-api-token-here
```


## Run the Interactive Setup

Run the initialization command to configure your organization, server, and site:

```bash
php artisan forge-init
```



This interactive command will:
1. Fetch and display your available Forge organizations
   ![Forge Init Step 1](./assets/1.jpg)

2. Fetch and display servers for the selected organization
   ![Forge Init Step 2](./assets/2.jpg)

3. Allow you to search and select your site
   ![Forge Init Step 3](./assets/3.jpg)

4. Automatically update your `.env` file with the configuration


## USAGE: Fetching Logs

Once configured, you can fetch different types of logs:

### Fetch All Logs (Recommended)

```bash
php artisan forge-fetch-logs
# or
php artisan forge-all-logs
```

This command will fetch Laravel application logs, Nginx access logs, and Nginx error logs in sequence.

### Fetch Individual Logs

#### Application Logs

```bash
php artisan forge-laravel-logs
```

Saves to `storage/logs/laravel.log` (customizable in config).

#### Nginx Access Logs

```bash
php artisan forge-nginx-access-logs
```

Saves to `storage/logs/nginx/access.log` (customizable in config).

#### Nginx Error Logs

```bash
php artisan forge-nginx-error-logs
```

Saves to `storage/logs/nginx/error.log` (customizable in config).




## [Optional] Publishing Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="forge-logs-config"
```

This will create a `config/forge-logs.php` file with the following structure:

```php
return [
    'forge_token' => env('FORGE_TOKEN'),
    'forge_server_id' => env('FORGE_SERVER_ID'),
    'forge_site_id' => env('FORGE_SITE_ID'),
    'forge_organization' => env('FORGE_ORGANIZATION'),

    // Customize log file paths (relative to storage_path())
    'log_paths' => [
        'laravel' => 'logs/laravel.log',
        'nginx_access' => 'logs/nginx/access.log',
        'nginx_error' => 'logs/nginx/error.log',
    ],
];
```

### Customizing Log File Paths

You can customize where the logs are saved by modifying the `log_paths` array in `config/forge-logs.php`:

```php
'log_paths' => [
    'laravel' => 'logs/forge/laravel.log',           // Change path
    'nginx_access' => 'logs/forge/nginx-access.log', // Change path and filename
    'nginx_error' => 'logs/forge/nginx-error.log',   // Change path and filename
],
```

All paths are relative to the `storage_path()` directory. 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [FrankFlow](https://github.com/FrankFlow)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
