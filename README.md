# vaibhavpandeyvpz/kunfig
Helper library to easily merge & use multiple configuration files.

[![Build status][build-status-image]][build-status-url]
[![Code Coverage][code-coverage-image]][code-coverage-url]
[![Latest Version][latest-version-image]][latest-version-url]
[![Downloads][downloads-image]][downloads-url]
[![PHP Version][php-version-image]][php-version-url]
[![License][license-image]][license-url]

[![SensioLabsInsight][insights-image]][insights-url]

Install
-------
```bash
composer require vaibhavpandeyvpz/kunfig
```

Usage
-----
```php
<?php

/**
 * @desc Create a Kunfig\Config instance with some initial values
 *      passed into constructor.
 */
$config = new Kunfig\Config(array(
    'database' => array(
        'host' => 'localhost',
        'port' => 3306,
        'charset' => 'utf8mb4',
    ),
    'debug' => false,
));

// Get a value
$host = $config->database->host;

// Set a value
$config->database->host = 'xxxxxxxxxxxxx.xxxxxxxxxxxxx.us-west-2.rds.amazonaws.com';

$override = new Kunfig\Config(array(
    'database' => array(
        'name' => 'test',
        'user' => 'root',
        'password' => null,
    ),
));

/**
 * @desc You can mix two Kunfig\ConfigInterface; the latter one
 *      will override values in the original one.
 */
$config->mix($override);

$pdo = new PDO(
    "mysql:host={$config->database->host}:{$config->database->port};dbname={$config->database->name}",
    $config->database->user,
    $config->database->password
);
```

License
-------
See [LICENSE.md][license-url] file.

[build-status-image]: https://img.shields.io/travis/vaibhavpandeyvpz/kunfig.svg?style=flat-square
[build-status-url]: https://travis-ci.org/vaibhavpandeyvpz/kunfig
[code-coverage-image]: https://img.shields.io/codecov/c/github/vaibhavpandeyvpz/kunfig.svg?style=flat-square
[code-coverage-url]: https://codecov.io/gh/vaibhavpandeyvpz/kunfig
[latest-version-image]: https://img.shields.io/github/release/vaibhavpandeyvpz/kunfig.svg?style=flat-square
[latest-version-url]: https://github.com/vaibhavpandeyvpz/kunfig/releases
[downloads-image]: https://img.shields.io/packagist/dt/vaibhavpandeyvpz/kunfig.svg?style=flat-square
[downloads-url]: https://packagist.org/packages/vaibhavpandeyvpz/kunfig
[php-version-image]: http://img.shields.io/badge/php-5.3+-8892be.svg?style=flat-square
[php-version-url]: https://packagist.org/packages/vaibhavpandeyvpz/kunfig
[license-image]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[license-url]: LICENSE.md
[insights-image]: https://insight.sensiolabs.com/projects/2606a5ca-43c2-4db9-ba3f-007e13e31362/small.png
[insights-url]: https://insight.sensiolabs.com/projects/2606a5ca-43c2-4db9-ba3f-007e13e31362
