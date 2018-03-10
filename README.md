Package Skeleton
================

[![Build Status](http://img.shields.io/travis/mcampbell508/git-flamethrower.svg)](https://travis-ci.org/mcampbell508/git-flamethrower)
[![Total Downloads](http://img.shields.io/packagist/dm/mcampbell508/git-flamethrower.svg)](https://packagist.org/packages/mcampbell508/git-flamethrower)
[![Latest Stable Version](http://img.shields.io/packagist/v/mcampbell508/git-flamethrower.svg)](https://packagist.org/packages/mcampbell508/git-flamethrower)
[![License](http://img.shields.io/badge/license-MIT-lightgrey.svg)](https://github.com/mcampbell508/git-flamethrower/blob/master/LICENSE)


:package_description

- [Installation](#installation)
- [Usage](#usage)
- [Code Style](#code-style)
- [Testing](#testing)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


Installation
------------

Add the git-flamethrower package to your `composer.json` file.

``` json
{
    "require": {
        "mcampbell508/git-flamethrower": "1.0.*"
    }
}
```

Or via the command line in the root of your Laravel installation.

``` bash
$ composer require "mcampbell508/git-flamethrower:1.0*"
```

Usage
-----

``` php
use MCampbell508\Git\Flamethrower;

$skeleton = new Skeleton();
echo $skeleton->echoPhrase('Hello, World!');

```

Code Style
-------

This project follows the following code style guidelines:

- [PSR-2](http://www.php-fig.org/psr/psr-2/) & [PSR-4](http://www.php-fig.org/psr/psr-4/) coding style guidelines.
- Some chosen [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) rules.


``` bash
$ php vendor/bin/php-cs-fixer fix
```


Testing
-------

``` bash
$ php vendor/bin/phpunit
```

Contributing
------------

Please see [CONTRIBUTING](https://github.com/mcampbell508/git-flamethrower/blob/master/CONTRIBUTING.md) for details.


Credits
-------
- [All Contributors](https://github.com/mcampbell508/git-flamethrower/contributors)

License
-------

The MIT License (MIT). Please see [License File](https://github.com/mcampbell508/git-flamethrower/blob/master/LICENSE) for more information.
