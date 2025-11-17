# Coercion — Package Specification

> **Cluster:** `language`
> **Language:** `php`
> **Milestone:** `m1`
> **Repo:** `https://github.com/decodelabs/coercion`
> **Role:** Type casting

This document describes the purpose, contracts, and design of **Coercion** within the Decode Labs ecosystem.

It is aimed at:

- Developers **using** this package.
- Contributors **maintaining or extending** it.
- Tools and AI assistants that need to reason about its behaviour.

---

## 1. Overview

### 1.1 Purpose

Coercion provides simple, consistent utilities for safely converting mixed-type values to specific PHP types. It addresses the common need to handle `mixed` parameters while maintaining strict type safety for static analysis tools like PHPStan.

Use this package when you need to:
- Convert user input or configuration values to strongly-typed properties
- Handle type coercion in a way that satisfies strict static analysis
- Provide consistent error handling when type conversion fails

The package offers three distinct coercion strategies:
- **`as*` methods** — throw exceptions on failure (strict validation)
- **`try*` methods** — return `null` on failure (optional values)
- **`to*` methods** — return default values on failure (forgiving conversion)

### 1.2 Non-Goals

- It does **not** perform validation beyond basic type conversion (use `decodelabs/lucid` for validation)
- It intentionally avoids complex type inference or automatic value transformation beyond straightforward coercion
- It does not provide serialization or unserialization utilities
- It does not handle recursive type coercion for complex nested structures beyond arrays and iterables

---

## 2. Role in the Ecosystem

### 2.1 Cluster & Positioning

- **Cluster:** `language` (see Chorus taxonomy)
- Coercion is a **foundational** utility within the language cluster, providing low-level type conversion primitives used widely across the ecosystem
- Many Decode Labs packages depend on Coercion for handling mixed inputs, particularly when processing user data, configuration, or external APIs
- It defines a consistent pattern for type coercion that other packages can rely on

### 2.2 Typical Usage Contexts

Coercion is commonly used:
- In **HTTP request handling** to convert query parameters, form data, or JSON payloads to typed properties
- In **CLI commands** when parsing command-line arguments and options
- During **bootstrapping** when reading configuration files or environment variables
- In **data pipelines** when transforming external data sources to internal types
- Throughout the codebase wherever `mixed` values need to be safely converted to specific types

---

## 3. Public Surface

> Focus on *conceptual* API, not every symbol.

### 3.1 Key Types

- `DecodeLabs\Coercion` — static utility class providing all coercion methods

This package exposes a single public class with static methods. There are no interfaces, traits, or value objects as part of the public API.

### 3.2 Main Entry Points

All interaction with Coercion happens through static methods on the `Coercion` class:

- `Coercion::asString($value)` — primary string coercion (throws on failure)
- `Coercion::asInt($value)` — primary integer coercion (throws on failure)
- `Coercion::asArray($value)` — primary array coercion (throws on failure)
- Similar `as*`, `try*`, and `to*` methods for other types

The naming convention is consistent across all types:
- Methods prefixed with `as` throw exceptions if coercion fails
- Methods prefixed with `try` return `null` if coercion fails
- Methods prefixed with `to` return default values if coercion fails

---

## 4. Dependencies

### 4.1 Direct Decode Labs Dependencies

- `decodelabs/exceptional` — used for throwing structured exceptions when coercion fails

Exceptional provides the exception factory (`Exceptional::InvalidArgument()`) used throughout Coercion to throw descriptive errors when values cannot be coerced.

### 4.2 External Dependencies

- None required for runtime operation

PHP 8.4+ built-in types and standard library classes (`DateTime`, `DateInterval`, `ReflectionClass`, etc.) are used directly.

See `composer.json` for supported PHP versions.

---

## 5. Behaviour & Contracts

### 5.1 Invariants

- All `as*` methods must throw `Exceptional::InvalidArgument` when coercion fails, never return default values
- All `try*` methods must return `null` (not `false` or empty strings) when coercion fails
- All `to*` methods must never throw exceptions; they always return a value of the requested type
- Methods must never mutate the input value; coercion produces new values
- String coercion respects enum values: `BackedEnum` with string values uses the value, with int values uses the name; `UnitEnum` uses the name

### 5.2 Input & Output Contracts

**String Coercion:**
- Accepts: strings, `Stringable` objects, numerics, enums, generators (yielding stringable values), closures returning stringable values
- Returns: non-empty strings by default when using `tryString($value, $nonEmpty = false)`; empty strings can be returned if `$nonEmpty = false`
- Special handling: generators are converted by joining yielded values; closures are invoked if they take no parameters

**Integer/Float Coercion:**
- Accepts: numeric values, booleans, enums (uses value for `BackedEnum`, index for `UnitEnum`), stringable numerics
- Returns: appropriate numeric type
- Clamping methods (`clampInt`, `clampFloat`, `clampDegrees`) accept `null` min/max to indicate no bound

**Array/Iterable Coercion:**
- Accepts: arrays, `Traversable` objects, `stdClass`, closures returning generators
- Returns: arrays or iterables preserving keys where possible
- `toArray` wraps non-iterable values in a single-element array

**Object Coercion:**
- Accepts: objects, arrays, `stdClass`
- `asObject`/`tryObject` accept any object type
- `asStdClass`/`tryStdClass` convert objects to `stdClass` via reflection

**DateTime/DateInterval Coercion:**
- Accepts: `DateTimeInterface` instances, strings, timestamps (numeric), `DateInterval` instances
- Returns: `DateTime` instances (not `DateTimeImmutable` for `asDateTime`)
- `tryDateTime` can convert `DateInterval` by adding to current time

**Type Coercion:**
- `asType`/`tryType` perform instanceof checks only; no conversion logic
- Used for type assertions when you already have an object

**Lazy Loading:**
- `newLazyGhost` and `newLazyProxy` wrap PHP 8.4's reflection-based lazy loading features
- Provide convenient access to `ReflectionClass::newLazyGhost()` and `ReflectionClass::newLazyProxy()`

---

## 6. Error Handling

### 6.1 Exception Types

- `Exceptional::InvalidArgument` — thrown by all `as*` methods when coercion fails
- This exception is provided by `decodelabs/exceptional` and implements the Decode Labs exception pattern

### 6.2 Error Strategy

Coercion follows a **fail-fast** approach for `as*` methods:
- Exceptions are thrown immediately when coercion is impossible
- Error messages are descriptive, indicating which type was expected
- No silent failures or warnings

For `try*` and `to*` methods:
- Failures are indicated by return values (`null` or defaults)
- No exceptions are thrown

This aligns with the Decode Labs strategy of using Exceptional for all exceptions, providing structured error information and better debugging context.

---

## 7. Configuration & Extensibility

### 7.1 Configuration

Coercion requires no configuration. All behavior is determined by method parameters and input values.

### 7.2 Extension Points

Coercion is not designed to be extended. It provides a fixed set of coercion strategies that are intended to be consistent across the ecosystem.

If custom coercion logic is needed:
- Implement wrapper functions or helper classes in your application
- Consider contributing to Coercion if the need is general enough for the ecosystem

There are no hooks, events, or plugin mechanisms.

---

## 8. Interactions with Other Packages

Coercion is used extensively throughout the Decode Labs ecosystem:

- **`decodelabs/enumerable`** — uses Coercion for enum value conversion
- **`decodelabs/atlas`** — uses Coercion when reading filesystem metadata
- **`decodelabs/terminus`** — uses Coercion for CLI argument parsing
- **`decodelabs/commandment`** — uses Coercion for command parameter processing
- **`decodelabs/lucid`** — may use Coercion as a foundation for value sanitization
- **`decodelabs/collections`** — uses Coercion for type conversions in collection operations
- **`decodelabs/elementary`**, **`decodelabs/tagged`** — use Coercion when processing markup data
- **`decodelabs/harvest`** — uses Coercion for HTTP request/response value handling
- **`decodelabs/fabric`** — uses Coercion throughout for framework-level type handling

Many other packages in the ecosystem depend on Coercion as a foundational utility for handling mixed input types in a type-safe manner.

---

## 9. Usage Examples

### 9.1 Basic Usage

Converting user input to typed properties:

```php
use DecodeLabs\Coercion;

final class UserConfig
{
    public function __construct(
        array $data,
    ) {
        $this->name = Coercion::asString($data['name']); // Throws if missing/invalid
        $this->age = Coercion::tryInt($data['age']); // Returns null if invalid
        $this->isActive = Coercion::toBool($data['isActive'] ?? false);
    }

    public readonly string $name;
    public ?int $age;
    public bool $isActive;
}
```

### 9.2 Handling Optional Values

Using `try*` methods for optional configuration:

```php
$timeout = Coercion::tryInt($config['timeout']) ?? 30;
$message = Coercion::tryString($config['message'], nonEmpty: true) ?? 'Default message';
```

### 9.3 Array Conversion

Converting various iterable types to arrays:

```php
$array = Coercion::asArray($someIterable); // Works with arrays, Traversable, stdClass
$array = Coercion::toArray($value); // Wraps non-iterables in array
```

### 9.4 DateTime Handling

Flexible date/time coercion:

```php
$date = Coercion::asDateTime('2024-01-01'); // String
$date = Coercion::asDateTime(1704067200); // Timestamp
$date = Coercion::toDateTime(null); // Defaults to 'now'
```

### 9.5 Enum Support

Automatic enum value extraction:

```php
enum Status : string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

$status = Status::Active;
$string = Coercion::asString($status); // 'active'
$index = Coercion::asInt($status); // Would use name or index depending on enum type
```

---

## 10. Implementation Notes (For Contributors)

### 10.1 Internal Architecture

Coercion is implemented as a single static class with no internal state. Methods are organized by target type:
- String coercion methods
- Boolean coercion methods
- Integer coercion methods
- Float coercion methods
- Array/iterable coercion methods
- Object coercion methods
- DateTime/DateInterval coercion methods
- Type assertion methods
- Lazy loading helper methods

The implementation uses PHP's native type juggling where safe, combined with explicit checks for edge cases (enums, generators, closures).

Special handling includes:
- Reflection-based inspection of closures to determine if they're parameterless generators
- Enum value extraction with fallback to name/index
- Generator iteration and joining for string conversion

### 10.2 Performance Considerations

- Coercion methods are designed to be fast for common cases (direct type checks)
- Reflection is used sparingly (closures, lazy loading) and results are not cached
- Generator handling involves iteration, which may be expensive for large generators
- No memoization or caching is performed

For high-frequency paths, prefer `try*` methods over `as*` if you can handle `null` efficiently, as it avoids exception overhead.

### 10.3 Gotchas & Historical Decisions

- **`asObject` vs `asStdClass`**: `asObject` actually calls `asStdClass` internally but returns `object`. This is intentional to allow `asObject` to accept any object type, while `asStdClass` specifically converts to `stdClass`.
- **DateTime vs DateTimeImmutable**: `asDateTime` and `tryDateTime` return `DateTime` instances, not `DateTimeImmutable`. There are separate `asDateTimeImmutable` methods for immutable dates. This maintains compatibility with code expecting mutable `DateTime` objects.
- **Enum index calculation**: `getEnumIndex` iterates through enum cases to find the index. This is O(n) but necessary since PHP doesn't provide a direct way to get enum case index.
- **Generator handling**: Generators are eagerly consumed for string/array conversion. This means they cannot be reused after coercion.

---

## 11. Testing & Quality

### 11.1 Testing Strategy

Current test coverage is minimal (scores: code 4.5, readme 3, docs 0, tests 0).

The `tests/` directory contains a single PHPStan-focused test file (`TestDateTime.php`) that demonstrates type inference for static analysis tools.

Comprehensive unit tests covering all coercion scenarios would be valuable but are not yet implemented.

### 11.2 Quality Signals

- **Code quality**: High (4.5/5) — well-structured, type-safe, uses modern PHP features appropriately
- **Readme quality**: Moderate (3/5) — documents API surface adequately but could provide more examples
- **Documentation**: None (0/5) — this spec is the first comprehensive documentation
- **Test coverage**: None (0/5) — functional tests are needed

Known gaps:
- Missing comprehensive test suite
- Limited edge case documentation
- No performance benchmarks

---

## 12. Roadmap & Future Ideas

Potential improvements under consideration:

- **Comprehensive test suite** — unit tests for all coercion methods and edge cases
- **Performance optimization** — caching of reflection results where appropriate
- **Additional type support** — consideration of additional target types based on ecosystem needs
- **Better enum handling** — if PHP provides better enum introspection in future versions

No breaking changes are currently planned. The API is stable and widely used across the ecosystem.

---

## 13. References

- **Chorus docs:**
  - Architecture principles
  - Taxonomy & clusters (language cluster)
  - Error handling strategy (Exceptional pattern)
- **Related packages:**
  - `decodelabs/exceptional` — exception handling
  - `decodelabs/lucid` — validation (may use Coercion internally)
  - `decodelabs/nuance` — type inspection tools

```text
https://github.com/decodelabs/coercion
https://github.com/decodelabs/chorus
```

---

> This spec is intended to stay in sync with the **actual behaviour** of the package.
> When you make significant changes to the public surface or semantics, please update this document and, where applicable, add or update ADRs in Chorus.

