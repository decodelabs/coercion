# Coercion

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/coercion?style=flat)](https://packagist.org/packages/decodelabs/coercion)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/coercion.svg?style=flat)](https://packagist.org/packages/decodelabs/coercion)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/coercion.svg?style=flat)](https://packagist.org/packages/decodelabs/coercion)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/coercion/integrate.yml?branch=develop)](https://github.com/decodelabs/coercion/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/coercion?style=flat)](https://packagist.org/packages/decodelabs/coercion)


### Simple tools for managing PHP types

Coercion offers simple tools to help neatly handle coercion of mixed parameters, especially useful when dealing with higher level static analysis test which require strict type handling.

_Get news and updates on the [DecodeLabs blog](https://blog.decodelabs.com)._

---


## Installation

Install via Composer:

```bash
composer require decodelabs/coercion
```

## Usage

Pass any mixed value to the available coerce methods to ensure input types in constructors:

```php
use DecodeLabs\Coercion;

class MyClass {

    protected string $string;
    protected ?string $optionalString;
    protected int $int;

    public function __construct(array $params) {
        $this->string = Coercion::toString($params['maybeString']);
        $this->optionalString = Coercion::toStringOrNull($params['maybeString']);
        $this->int = Coercion::toInt($params['maybeInt']);
    }
}
```

## Licensing
Coercion is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
