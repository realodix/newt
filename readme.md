<h1>UrlHub</h1>

[![MadeWithLaravel.com shield](https://madewithlaravel.com/storage/repo-shields/1049-shield.svg)](https://madewithlaravel.com/p/plur/shield-link)
[![LaravelVersion](https://img.shields.io/badge/Laravel-7-f56857.svg?style=flat-square)](https://laravel.com/docs/7.x)
![PHPVersion](https://img.shields.io/badge/PHP-%5E7.3-777BB4.svg?style=flat-square)
[![GitHub license](https://img.shields.io/github/license/realodix/newt.svg?style=flat-square)](https://github.com/realodix/newt/blob/master/LICENSE) <br>
[![StyleCI shield](https://github.styleci.io/repos/146186200/shield)](https://github.styleci.io/repos/146186200)
[![Build Status](https://travis-ci.org/realodix/urlhub.svg?branch=master)](https://travis-ci.org/realodix/urlhub)
[![Coverage Status](https://coveralls.io/repos/github/realodix/urlhub/badge.svg?branch=master)](https://coveralls.io/github/realodix/urlhub)

> **Warning: UrlHub is still in development**, constantly being optimized and isn't still stable enough to be used in production environments. We could change and / or remove functions in any moment.

> **Whatever your idea, feel free to send a pull request** 😃

UrlHub was created, and is maintained by [Budi Hermawan](https://github.com/realodix), and is an open-source, easy-to-use but powerful URL shortener. It allows you to host your own URL shortener, and gives you many useful features.

### Features
* URL Shortener.
* Customized short URL's(ex: example.com/laravel).
* QR code generator for each short link.
* Sortable list of shortened URLs.
* Written in [PHP](https://www.php.net/) and [Laravel 7](https://laravel.com/docs/7.x).
* [Datatables](https://datatables.net/) with server-side processing.
* Modern and simple interface.
* Made with :heart: &amp; :coffee:.

### Screenshots
| ![screenshot](https://i.imgur.com/GFvIeBg.png) | ![screenshot](https://i.imgur.com/nJGVGHT.png) | ![screenshot](https://i.imgur.com/CpMAeaq.png) | ![screenshot](https://i.imgur.com/imRINvR.jpg) |
|-|-|-|-|


## Requirements
* All requirements by [Laravel](https://laravel.com/docs/installation#server-requirements) & [PHPUnit 9](https://phpunit.readthedocs.io/en/9.0/installation.html#requirements) - PHP >= 7.3, [Composer](https://getcomposer.org/) and such.
* MySQL or MariaDB.


## Quick Start
### Installation Instructions
1. Run `composer install`.

2. Rename `.env.example` file to `.env` or run `cp .env.example .env`.

   Update `.env` to your specific needs. Don't forget to set `DB_USERNAME` and `DB_PASSWORD` with the settings used behind.

3. Run `php artisan key:generate`.

4. Run `php artisan migrate --seed`.

5. Run `php artisan serve`.

   After installed, you can access `http://localhost:8000` in your browser.

6. Login

   | Email             | Username | Password | Access       |
   |-------------------|----------|----------|--------------|
   | admin@urlhub.test | admin    | admin    | Admin Access |
   | user@urlhub.test  | user     | user     | User Access  |


### Compiling assets with Laravel Mix

#### Using Yarn
1. `yarn`
2. `yarn dev` or `yarn prod`

    *You can watch assets with `yarn watch`*

#### Using NPM
1. `npm install`
2. `npm run dev` or `npm run prod`

    *You can watch assets with `npm run watch`*


## Contributing
Thank you for considering contributing to UrlHub. Any useful suggestion and pull requests are welcomed.

Please do the following:

1. Fork the repository.
2. Hack on a separate topic branch created from the latest `master`.
3. Commit and push the topic branch.
4. Make a pull request.
5. Welcome to the club :sunglasses: and thank you for helping out!

### Running Tests

From the projects root folder run `./vendor/bin/phpunit` (VSCode Task `Run test`) or [Artisan Test Runner](https://laravel.com/docs/testing#artisan-test-runner) `php artisan test`

![screenshot](https://i.imgur.com/ShPy5Iv.jpg)


## License
UrlHub is an open-source software licensed under the [MIT license](https://github.com/realodix/urlhub/blob/master/LICENSE).
