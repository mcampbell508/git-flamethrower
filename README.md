Git Review
================

A tool designed for improving development workflow with Git

- [Installation](#installation)
- [Usage](#usage)
- [Code Style](#code-style)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

Installation
------------

Add the `git-review` package to your `composer.json` file.

``` json
{
    "require": {
        "theshopworks/git-review": "1.0.*"
    }
}
```

Or via the command line in the root of your Laravel installation.

``` bash
$ composer require "theshopworks/git-review:1.0*"
```

Usage
-----

``` php
vendor/bin/git-review
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

Please see [CONTRIBUTING](https://gitlab.com/theshopworks/git-review/blob/master/CONTRIBUTING.md) for details.

License
-------

The MIT License (MIT). Please see [License File](https://gitlab.com/theshopworks/git-review/blob/master/LICENSE) for more information.
