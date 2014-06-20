
# PHP Project Template

It seems like modern php projects have a lot of duplicated needs- test suite configuration, licenses, continuous
integration setup, etc etc. This project is a basic template for all of that with some sane defaults and placeholders
for where things like the project name.


# {{ name }} [![Build Status](https://travis-ci.org/tedivm/{{ name }}.svg?branch=master)](https://travis-ci.org/tedivm/{{ name }})

[![License](http://img.shields.io/packagist/l/tedivm/{{ name }}.svg)](https://github.com/tedivm/{{ name }}/blob/master/LICENSE)
[![Latest Stable Version](http://img.shields.io/github/release/tedivm/{{ name }}.svg)](https://packagist.org/packages/tedivm/{{ name }})
[![Coverage Status](http://img.shields.io/coveralls/tedivm/{{ name }}.svg)](https://coveralls.io/r/tedivm/{{ name }}?branch=master)
[![Total Downloads](http://img.shields.io/packagist/dt/tedivm/{{ name }}.svg)](https://packagist.org/packages/tedivm/{{ name }})


## Installing

### Composer

Installing {{ name }} can be done through a variety of methods, although Composer is
recommended.

Until {{ name }} reaches a stable API with version 1.0 it is recommended that you
review changes before even Minor updates, although bug fixes will always be
backwards compatible.

```
"require": {
  "tedivm/{{ name }}": "0.5.*"
}
```

### Github

Releases of {{ name }} are available on [Github](https://github.com/tedivm/{{ name }}/releases).
