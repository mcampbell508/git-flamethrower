title: Contributing
---
## Development

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Merge Requests on [GitLab](https://gitlab.com/theshopworks/git-review).

### Before You Start

Please follow the coding style:

- Follow [PHP-FIG PSR2 Coding Standards](https://www.php-fig.org/psr/psr-2/).
- Use 4 spaces over tabs.
- Use [PHP-CS-Fixer rules](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
- Use [PHPStan](https://github.com/phpstan/phpstan) level 7, for static analysis of code.
- Use [PHPUnit](https://phpunit.de/) for automated tests.

### Workflow

1. Fork [theshopworks/git-review](https://gitlab.com/theshopworks/git-review).
2. Clone the repository to your computer and install dependencies.

    {% code %}
    $ git clone https://gitlab.com/<username>/git-review.git
    $ cd git-review
    $ composer install
    {% endcode %}

3. Create a feature branch.

    {% code %}
    $ git checkout -b new-feature
    {% endcode %}

4. Start hacking.
5. Push the branch:

    {% code %}
    $ git push origin new-feature
    {% endcode %}

6. Create a merge request and describe the change.

## Merge Requests

- **Coding Syntax** - Please keep the code syntax consistent with the rest of the package.

- **Add unit tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behavior** - Make sure the README and any other relevant documentation are kept up-to-date.

- **Use types over PhpDocBlocks** - Try to declare arguments types and return types, over using unnecessary PhpDocBlocks.

- **Consider our release cycle** - We try to follow [semver](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create topic branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful.
