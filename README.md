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

Pass any mixed value to the available coerce methods to ensure input types in constructors.
Methods beginning with `as` will throw an exception if the value cannot be coerced to the desired type.
Methods beginning with `try` will return `null` if the value cannot be coerced to the desired type.
Methods beginning with `to` will return a default value if the value cannot be coerced to the desired type.

```php
use DecodeLabs\Coercion;

class MyClass {

    protected string $string;
    protected ?string $optionalString;
    protected int $int;

    public function __construct(array $params) {
        $this->string = Coercion::asString($params['maybeString']); // Throw on error
        $this->string = Coercion::toString($params['maybeString']); // Convert to empty string on error
        $this->optionalString = Coercion::tryString($params['maybeString']);
    }
}
```

### Available Methods

```php
// String
Coercion::asString($value): string // Throws exception on error
Coercion::tryString($value, bool $nonEmpty = true): ?string
Coercion::toString($value): string
Coercion::isStringable($value): bool

// Bool
Coercion::toBool($value): bool
Coercion::tryBool($value): ?bool
Coercion::parseBool($value): ?bool // Only returns true for strings if string is boolsy

// Int
Coercion::asInt($value): int // Throws exception on error
Coercion::tryInt($value): ?int
Coercion::toInt($value): int
Coercion::clampInt($value, int $min, int $max): int

// Float
Coercion::asFloat($value): float // Throws exception on error
Coercion::tryFloat($value): ?float
Coercion::toFloat($value): float
Coercion::clampFloat($value, float $min, float $max): float
Coercion::clampDegrees($value, float $min, float $max): float

// Array
Coercion::asArray($value): array // Throws exception on error
Coercion::tryArray($value): ?array
Coercion::toArray($value): array

// Iterable
Coercion::asIterable($value): iterable // Throws exception on error
Coercion::tryIterable($value): ?iterable
Coercion::toIterable($value): iterable
Coercion::iterableToArray($value): array

// Object
Coercion::asObject($value): object // Throws exception on error
Coercion::tryObject($value): ?object
Coercion::toObject($value): object

// stdClass
Coercion::asStdClass($value): stdClass // Throws exception on error
Coercion::tryStdClass($value): ?stdClass
Coercion::toStdClass($value): stdClass

// Type
Coercion::asType($value, class-string<T> $type): T // Throws exception on error
Coercion::tryType($value, class-string<T> $type): ?T

// Lazy
Coercion::newLazyGhost(class-string<T> $type, callable $callback): T
Coercion::newLazyProxy(class-string<T> $type, callable $callback): T

// DateTime
Coercion::asDateTime($value): DateTimeInterface // Throws exception on error
Coercion::tryDateTime($value): ?DateTimeInterface
Coercion::toDateTime($value): DateTimeInterface // Defaults to now

// DateInterval
Coercion::asDateInterval($value): DateInterval // Throws exception on error
Coercion::tryDateInterval($value): ?DateInterval
```


## Licensing
Coercion is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
