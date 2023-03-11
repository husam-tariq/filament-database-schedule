# Manage your Laravel Task Scheduling in a Filament interface and save schedules to the database.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/husam-tariq/filament-database-schedule.svg?style=flat-square)](https://packagist.org/packages/husam-tariq/filament-database-schedule)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/husam-tariq/filament-database-schedule/run-tests?label=tests)](https://github.com/husam-tariq/filament-database-schedule/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/husam-tariq/filament-database-schedule/Check%20&%20fix%20styling?label=code%20style)](https://github.com/husam-tariq/filament-database-schedule/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/husam-tariq/filament-database-schedule.svg?style=flat-square)](https://packagist.org/packages/husam-tariq/filament-database-schedule)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require husam-tariq/filament-database-schedule
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-database-schedule-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-database-schedule-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-database-schedule-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filament-database-schedule = new HusamTariq\FilamentDatabaseSchedule();
echo $filament-database-schedule->echoPhrase('Hello, HusamTariq!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hussam Tariq](https://github.com/husam-tariq)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
