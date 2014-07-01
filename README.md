# Spark [![Build Status](https://travis-ci.org/tedivm/Spark.svg?branch=master)](https://travis-ci.org/tedivm/Spark)

[![License](http://img.shields.io/packagist/l/tedivm/Spark.svg)](https://github.com/tedivm/Spark/blob/master/LICENSE)
[![Latest Stable Version](http://img.shields.io/github/release/tedivm/Spark.svg)](https://packagist.org/packages/tedivm/Spark)
[![Coverage Status](http://img.shields.io/coveralls/tedivm/Spark.svg)](https://coveralls.io/r/tedivm/Spark?branch=master)

It seems like modern php projects have a lot of duplicated needs- test suite configuration, licenses, continuous
integration setup, etc etc. All of these serve a real need, and a project can't be considered mature without them, but
there is also something to be said for reducing the barrier of entry on setting up new projects.

Spark is a project template system that creates new projects with a simple command line argument. It supports a variety
of project types that can be specified with an argument, defaulting to the "library" package-


```shell
    $ spark create AcmeLibrary
```

Creating a different type of project, such as a cli application, is trivial-
```shell
    $ spark create AcmeShellApplication cli
```

In each of these cases a new project will be created in your current directory in a folder with the project name (-d to
pick a different location).

Getting a list of available project types is simple using the show command, which can also display additional
information. about each package.

```shell
    $ spark show packages
    $ spark show packages cli
```



## Installing

### Composer

To install Spark using Composer, install Composer and issue the following command:

```shell
    $ ./composer.phar global require tedivm/spark @stable
```

If you haven't already, add ``~/.composer/vendor/bin`` to your ``PATH``


```shell
    export PATH="$PATH:$HOME/.composer/vendor/bin"
```

### Github

Releases of Spark are available on [Github](https://github.com/tedivm/Spark/releases). Download the spark.phar file and
run it locally or move it to your system bin.

```shell
    sudo cp spark.phar /usr/local/bin/spark
    sudo chmod a+x /usr/local/bin/spark
```
