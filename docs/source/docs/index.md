title: Documentation
---
Welcome to the Git Review documentation.

## What is Git-Review?

Git-Review has been design to simplify and improve a development workflow using Git, which hopefully allows to ship things quicker as an individual or a team.

This project offers a range of tools that *could* speed up development workflow, either through commands that make Git more powerful or using scripts that hook into Git events, using [Git hooks](https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks).

## Installation

It only takes a few minutes to set up Git Review. 

### Requirements

Installing Git Review is quite easy. However, you do need to have a couple of other things installed first:

- [Git](http://git-scm.com/)
- [PHP `7.1.3 and above`](http://php.net)
- [Composer](https://getcomposer.org)
    - [Linux / Unix / OSX installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
    - [Windows installation](https://getcomposer.org/doc/00-intro.md#installation-windows)

If your computer already has these, congratulations! Just install `git-review` with composer:

``` bash
$ composer require theshopworks/git-review
```

If not, please follow the following instructions to install all the requirements.

### Install Git

- Windows: Download & install [git](https://git-scm.com/download/win).
- Mac: Install it with [Homebrew](http://mxcl.github.com/homebrew/), [MacPorts](http://www.macports.org/) or [installer](http://sourceforge.net/projects/git-osx-installer/).
- Linux (Ubuntu, Debian): `sudo apt-get install git-core`
- Linux (Fedora, Red Hat, CentOS): `sudo yum install git-core`

### Install `git-review`

Once all the requirements are installed, you can install Hexo with composer:

``` bash
$ composer require theshopworks/git-review
```
