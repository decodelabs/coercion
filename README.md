# Coercion

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/coercion?style=flat)](https://packagist.org/packages/decodelabs/coercion)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/coercion.svg?style=flat)](https://packagist.org/packages/decodelabs/coercion)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/coercion.svg?style=flat)](https://packagist.org/packages/decodelabs/coercion)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/decodelabs/coercion/PHP%20Composer)](https://github.com/decodelabs/coercion/actions/workflows/php.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/coercion?style=flat)](https://packagist.org/packages/decodelabs/coercion)


### Simple tools for managing PHP types

Coercion offers simple tools to help neatly handle coercion of mixed parameters, especially useful when dealing with higher level static analysis test which require strict type handling.

## Installation

Install via Composer:

```bash
composer require decodelabs/coercion
```

### PHP version

_Please note, the final v1 releases of all Decode Labs libraries will target **PHP8** or above._

Current support for earlier versions of PHP will be phased out in the coming months.


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
