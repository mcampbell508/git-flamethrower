Git Review
================

A tool designed for improving development workflow with Git

- [Installation](#installation)
- [Usage](#usage)
- [Code Style](#code-style)
- [Testing](#testing)
- [Documentation](#docs)
- [Contributing](#contributing)
- [License](#license)

Installation
------------

Add the `git-review` package to your `composer.json` file.

``` json
{
    "require": {
        "theshopworks/git-review": "^0.1"
    }
}
```

Or via the command line in the root of your Laravel installation.

``` bash
$ composer require "theshopworks/git-review:^0.1"
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

Documentation
-------------

Documentation for this project can be found in the `docs/` folder. We use a static site generator [Hexo](https://hexo.io/), to generate our documentation. 

The documentation is licensed under a [Creative Commons Attribution 4.0 Generic License](https://creativecommons.org/licenses/by/4.0/). It is attributed to Tommy Chen, and their original version can be found [here](https://github.com/hexojs/site). Please see, the [LICENSE](./docs/LICENSE) 

The docs are automatically deployed to https://theshopworks.gitlab.io/git-review, on every merge of `master`.

To build the docs locally, you will need to do the following:

> REQUIREMENT: You will need to install [Yarn](https://yarnpkg.com/lang/en/docs/install), before you can genarate the docs.

```
cd docs/
yarn install
./node_modules/.bin/hexo server
```

To generate the `/public` files, you can also use `./node_modules/.bin/hexo generate`.

Contributing
------------

Please see [CONTRIBUTING](https://gitlab.com/theshopworks/git-review/blob/master/CONTRIBUTING.md) for details.

License
-------

The MIT License (MIT). Please see [License File](https://gitlab.com/theshopworks/git-review/blob/master/LICENSE) for more information.
