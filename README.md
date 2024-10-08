# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rickodev/phone-reset.svg?style=flat-square)](https://packagist.org/packages/rickodev/phone-reset)
[![Total Downloads](https://img.shields.io/packagist/dt/rickodev/phone-reset.svg?style=flat-square)](https://packagist.org/packages/rickodev/phone-reset)
![GitHub Actions](https://github.com/rickodev/phone-reset/actions/workflows/main.yml/badge.svg)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require rickodev/phone-reset
```

## Usage

```php
php artisan vendor:publish --tag=phone-reset-migrations
php artisan migrate
```


```php
use RickoDev\PhoneReset\Contracts\CanResetPhonePassword;

class User extends Authenticatable implements CanResetPhonePassword
{
    use HasFactory, Notifiable;

```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email author@domain.com instead of using the issue tracker.

## Credits

-   [Ricko Dev](https://github.com/rickodev)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


