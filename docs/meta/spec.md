# Coercion — Package Specification

> **Cluster:** `language`
> **Language:** `php`
> **Milestone:** `m1`
> **Repo:** `https://github.com/decodelabs/coercion`
> **Role:** Type casting

This document describes the purpose, contracts, and design of **Coercion** within the Decode Labs ecosystem.

It is aimed at:

- Developers **using** Coercion in their own applications or libraries.
- Contributors **maintaining or extending** Coercion.
- Tools and AI assistants that need to reason about its behaviour.

---

## 1. Overview

### 1.1 Purpose

Coercion provides simple tools for managing PHP types, offering a comprehensive set of methods to safely coerce mixed values to specific types. It helps handle type coercion of mixed parameters, especially useful when dealing with higher-level static analysis tests that require strict type handling. The package provides three patterns for each type: `as*` (throws on error), `try*` (returns null on error), and `to*` (returns default on error).

### 1.2 Non-Goals

Coercion does **not**:

- Provide validation or sanitization — it only handles type conversion
- Handle complex object transformations or mapping
- Provide schema validation or data structure validation
- Handle type inference or automatic type detection
- Provide serialization or deserialization capabilities
- Handle database type conversions or ORM integration

---

## 2. Role in the Ecosystem

### 2.1 Cluster & Positioning

- **Cluster:** `language` (see Chorus taxonomy)
- Coercion is a foundational language utility package that provides type coercion capabilities for the Decode Labs ecosystem. It sits at a low dependency level, depending only on Exceptional, and is used extensively throughout the ecosystem for handling mixed types safely. It's part of the language cluster alongside other type-related utilities.

### 2.2 Typical Usage Contexts

Typical places Coercion appears:

- Parameter handling in constructors and methods accepting mixed types
- Configuration value parsing and conversion
- Command-line argument processing
- Data transformation pipelines
- API input handling
- Database result processing
- Any code that needs to safely convert mixed values to specific types

Coercion is intended to be used whenever code needs to safely convert mixed values to specific types while maintaining static analysis compatibility and providing clear error handling.

---

## 3. Public Surface

> This section focuses on the conceptual API, not every symbol.

### 3.1 Key Types

The primary public type is:

- `DecodeLabs\Coercion`
  Static utility class providing all coercion methods. All methods are static and organized by target type.

### 3.2 Main Entry Points

The main usage pattern is through static methods on the `Coercion` class:

```php
use DecodeLabs\Coercion;

$string = Coercion::asString($value);  // Throws on error
$string = Coercion::tryString($value); // Returns null on error
$string = Coercion::toString($value);  // Returns default on error
```

---

## 4. Dependencies

### 4.1 Decode Labs

- `decodelabs/exceptional` (required)
  Used for exception handling. All `as*` methods throw `Exceptional::InvalidArgument` when coercion fails.

### 4.2 External

- None

### 4.3 Optional Integrations

- None

---

## 5. Behaviour & Contracts

### 5.1 Invariants

- `as*` methods always throw `Exceptional::InvalidArgument` if coercion fails
- `try*` methods always return `null` if coercion fails
- `to*` methods always return a sensible default if coercion fails
- String coercion handles closures (if parameterless), generators, enums, and Stringable objects
- Array coercion handles closures (if generator), stdClass, and Traversable objects
- Numeric coercion handles enums, Stringable objects, and boolean values
- DateTime coercion handles timestamps, DateInterval, and string formats
- All methods are stateless and have no side effects

### 5.2 Input & Output Contracts

**String Methods:**
- `asString(mixed $value): string` — Throws exception on error
- `tryString(mixed $value, bool $nonEmpty = false): ?string` — Returns null on error, optionally rejects empty strings
- `toString(mixed $value): string` — Returns empty string on error
- `isStringable(mixed $value): bool` — Checks if value can be converted to string

**Boolean Methods:**
- `toBool(mixed $value): bool` — Returns false on error
- `tryBool(mixed $value): ?bool` — Returns null on error
- `parseBool(mixed $value): ?bool` — Only returns true for strings if string is "booleany" (1, true, yes, on, etc.)

**Integer Methods:**
- `asInt(mixed $value): int` — Throws exception on error
- `tryInt(mixed $value): ?int` — Returns null on error
- `toInt(mixed $value): int` — Returns 0 on error
- `clampInt(mixed $value, ?int $min, ?int $max): ?int` — Clamps value to range, returns null if input is null

**Float Methods:**
- `asFloat(mixed $value): float` — Throws exception on error
- `tryFloat(mixed $value): ?float` — Returns null on error
- `toFloat(mixed $value): float` — Returns 0.0 on error
- `clampFloat(mixed $value, ?float $min, ?float $max): ?float` — Clamps value to range, returns null if input is null
- `clampDegrees(mixed $value, ?float $min, ?float $max): ?float` — Clamps degrees (0-359) with wraparound, returns null if input is null

**Array Methods:**
- `asArray(mixed $value): array` — Throws exception on error
- `tryArray(mixed $value): ?array` — Returns null on error
- `toArray(mixed $value): array` — Returns empty array or single-element array on error

**Iterable Methods:**
- `asIterable(mixed $value): iterable` — Throws exception on error
- `tryIterable(mixed $value): ?iterable` — Returns null on error
- `toIterable(mixed $value): iterable` — Returns empty array or single-element array on error
- `iterableToArray(iterable|Closure $value): array` — Converts iterable to array

**Object Methods:**
- `asObject(mixed $value): object` — Throws exception on error
- `tryObject(mixed $value): ?object` — Returns null on error
- `toObject(mixed $value): object` — Returns new stdClass on error

**stdClass Methods:**
- `asStdClass(mixed $value): stdClass` — Throws exception on error
- `tryStdClass(mixed $value): ?stdClass` — Returns null on error
- `toStdClass(mixed $value): stdClass` — Returns new stdClass on error

**Type Methods:**
- `asType(mixed $value, class-string<T> $type): T` — Throws exception on error
- `tryType(mixed $value, class-string<T> $type): ?T` — Returns null on error

**Lazy Methods:**
- `newLazyGhost(class-string<T> $type, callable $initializer): T` — Creates lazy ghost object
- `newLazyProxy(class-string<T> $type, callable $factory): T` — Creates lazy proxy object

**DateTime Methods:**
- `asDateTime(mixed $value): DateTimeInterface` — Throws exception on error
- `tryDateTime(mixed $value): ?DateTimeInterface` — Returns null on error
- `toDateTime(mixed $value): DateTimeInterface` — Returns current time on error
- `asDateTimeImmutable(mixed $value): DateTimeImmutable` — Throws exception on error
- `tryDateTimeImmutable(mixed $value): ?DateTimeImmutable` — Returns null on error
- `toDateTimeImmutable(mixed $value): DateTimeImmutable` — Returns current time on error

**DateInterval Methods:**
- `asDateInterval(mixed $value): DateInterval` — Throws exception on error
- `tryDateInterval(mixed $value): ?DateInterval` — Returns null on error
- `toDateInterval(mixed $value): DateInterval` — Returns zero interval on error

### 5.3 Special Behaviours

**Closure Handling:**
- String coercion: Executes parameterless closures and processes result
- Array/Iterable coercion: Executes generator closures (parameterless) and processes result

**Enum Handling:**
- String coercion: Uses enum name for UnitEnum, value or name for BackedEnum
- Integer coercion: Uses enum value for BackedEnum (if int), or enum index for UnitEnum

**Generator Handling:**
- String coercion: Iterates generator and joins string values
- Array coercion: Converts generator to array via `iterator_to_array()`

**DateTime Handling:**
- Accepts timestamps (numeric), DateInterval (adds to now), and string formats
- DateInterval coercion can convert DateTimeInterface (diff from now) or numeric values

**Clamping:**
- `clampInt` and `clampFloat` constrain values to min/max range
- `clampDegrees` wraps values around 0-359 range before clamping

---

## 6. Error Handling

- All `as*` methods throw `Exceptional::InvalidArgument` when coercion fails
- All `try*` methods return `null` when coercion fails (graceful degradation)
- All `to*` methods return sensible defaults when coercion fails (empty string, 0, empty array, etc.)
- `parseBool` returns `null` for non-boolean-like strings (strict parsing)
- `tryBool` returns `null` for empty strings, otherwise converts to boolean
- `clampInt` and `clampFloat` return `null` if input is `null` (preserves nullability)
- DateInterval parsing throws `Exceptional::InvalidArgument` if string cannot be parsed

---

## 7. Configuration & Extensibility

- Coercion is not configurable — all behaviour is fixed
- No extension points are provided — it's a utility class with static methods
- Custom coercion logic should be implemented separately or wrapped around Coercion methods

---

## 8. Interactions with Other Packages

### 8.1 Exceptional

Coercion uses Exceptional for all exception handling. All `as*` methods throw `Exceptional::InvalidArgument` when coercion fails, providing consistent error handling across the ecosystem.

---

## 9. Usage Examples

### 9.1 Basic Type Coercion

```php
use DecodeLabs\Coercion;

class MyClass {
    protected string $string;
    protected ?string $optionalString;
    protected int $int;

    public function __construct(array $params) {
        // Throw on error
        $this->string = Coercion::asString($params['maybeString']);
        
        // Return default on error
        $this->string = Coercion::toString($params['maybeString']);
        
        // Return null on error
        $this->optionalString = Coercion::tryString($params['maybeString']);
        
        // Integer coercion
        $this->int = Coercion::asInt($params['maybeInt']);
    }
}
```

### 9.2 Safe Coercion with Fallbacks

```php
use DecodeLabs\Coercion;

$value = $_GET['count'] ?? null;
$count = Coercion::tryInt($value) ?? 10; // Default to 10 if null

$value = $_GET['enabled'] ?? null;
$enabled = Coercion::parseBool($value) ?? false; // Strict boolean parsing
```

### 9.3 Array and Iterable Handling

```php
use DecodeLabs\Coercion;

$data = Coercion::asArray($mixedValue);
$iterable = Coercion::asIterable($mixedValue);
$array = Coercion::iterableToArray($iterable);
```

### 9.4 DateTime Handling

```php
use DecodeLabs\Coercion;

$date = Coercion::asDateTime('2025-05-16');
$date = Coercion::asDateTime(1715817600); // Timestamp
$date = Coercion::asDateTime(new DateInterval('P1D')); // Adds to now
$date = Coercion::toDateTime(null); // Returns current time
```

### 9.5 Clamping Values

```php
use DecodeLabs\Coercion;

$value = Coercion::clampInt($input, 0, 100); // Clamp to 0-100
$degrees = Coercion::clampDegrees($angle, 0, 180); // Wrap and clamp degrees
```

### 9.6 Type Checking

```php
use DecodeLabs\Coercion;

$object = Coercion::asType($value, MyClass::class);
$object = Coercion::tryType($value, MyClass::class); // Returns null if not instance
```

---

## 10. Implementation Notes (for Contributors)

### 10.1 Method Naming Conventions

- `as*`: Throws exception on error (assertive)
- `try*`: Returns null on error (safe)
- `to*`: Returns default on error (permissive)

### 10.2 Type Handling

- Enums are handled specially: BackedEnum uses value (if matching type) or name, UnitEnum uses name
- Generators are handled by iterating and processing values
- Closures are executed if parameterless (checked via reflection)
- Stringable objects are converted via `(string)` cast
- Numeric strings are handled via `is_numeric()` check

### 10.3 DateTime Parsing

- Numeric values are treated as timestamps
- DateInterval values are added to current time
- String values are parsed via DateTime constructor
- DateTimeImmutable uses `createFromInterface()` for conversion

### 10.4 DateInterval Parsing

- DateTimeInterface values are converted via `diff()` from now
- Numeric values are treated as seconds if small, timestamps if large
- String values are parsed via DateInterval constructor or `createFromDateString()`

### 10.5 Clamping Logic

- `clampInt` and `clampFloat` use `min()` and `max()` to constrain values
- `clampDegrees` wraps values around 0-359 range using modulo-like logic before clamping

### 10.6 Lazy Object Creation

- Uses PHP 8.4+ reflection methods `newLazyGhost()` and `newLazyProxy()`
- Requires reflection support for the target class

---

## 11. Testing & Quality

- **Code Quality Score:** 4.5/5
- **README Quality Score:** 3/5
- **Documentation Score:** 0/5 (this spec)
- **Test Coverage Score:** 0/5

See `composer.json` for supported PHP versions.

---

## 12. Roadmap & Future Ideas

- Add support for more complex type conversions
- Consider adding validation alongside coercion
- Add support for custom coercion rules
- Consider adding schema-based coercion
- Improve error messages with more context
- Add test coverage

---

## 13. References

- [Exceptional Package](https://github.com/decodelabs/exceptional) — Exception handling
- [Chorus Package Index](../../../chorus/config/packages.json) — Ecosystem metadata
