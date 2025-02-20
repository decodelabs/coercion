<?php

/**
 * @package Coercion
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use BackedEnum;
use Closure;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Generator;
use ReflectionClass;
use ReflectionFunction;
use stdClass;
use Stringable;
use Traversable;
use UnitEnum;

class Coercion
{
    /**
     * Coerce value to string
     */
    public static function asString(
        mixed $value
    ): string {
        if (null === ($value = static::tryString($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to string'
            );
        }

        return $value;
    }

    /**
     * Coerce value to string or null
     */
    public static function tryString(
        mixed $value,
        bool $nonEmpty = false
    ): ?string {
        if ($value instanceof BackedEnum) {
            $value = is_int($value->value) ?
                $value->name :
                $value->value;
        } elseif ($value instanceof UnitEnum) {
            $value = $value->name;
        }

        if (
            is_string($value) ||
            $value instanceof Stringable ||
            is_numeric($value)
        ) {
            $output = (string)$value;

            if (
                $nonEmpty &&
                $output === ''
            ) {
                return null;
            }

            return $output;
        }

        return null;
    }


    /**
     * Force value to be string
     */
    public static function toString(
        mixed $value
    ): string {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            $output = [];

            foreach ($value as $inner) {
                if (strlen($inner = static::toString($inner))) {
                    $output[] = $inner;
                }
            }

            return implode(' ', $output);
        }

        return (string)static::tryString($value);
    }

    /**
     * Is value stringable
     */
    public static function isStringable(
        mixed $value
    ): bool {
        return
            is_string($value) ||
            $value instanceof Stringable ||
            is_numeric($value);
    }





    /**
     * Coerce value to bool
     */
    public static function toBool(
        mixed $value
    ): bool {
        return (bool)static::tryBool($value);
    }

    /**
     * Coerce value to bool or null
     */
    public static function tryBool(
        mixed $value
    ): ?bool {
        if (
            $value === null ||
            is_bool($value)
        ) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower($value);

            return match ($value) {
                '' => null,
                '0', 'false', 'no', 'off' => false,
                default => true
            };
        }

        return (bool)$value;
    }

    /**
     * Coerce value to bool if boolsy or null
     */
    public static function parseBool(
        mixed $value
    ): ?bool {
        if (
            $value === null ||
            is_bool($value)
        ) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower($value);

            return match ($value) {
                '1', 'true', 'yes', 'on' => true,
                '0', 'false', 'no', 'off' => false,
                default => null
            };
        }

        if (is_numeric($value)) {
            return $value <> 0;
        }

        return null;
    }





    /**
     * Coerce value to int
     */
    public static function asInt(
        mixed $value
    ): int {
        if (null === ($value = static::tryInt($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to int'
            );
        }

        return $value;
    }

    /**
     * Coerce value to int or null
     */
    public static function tryInt(
        mixed $value
    ): ?int {
        if ($value instanceof BackedEnum) {
            $value = is_int($value->value) ?
                $value->value :
                static::getEnumIndex($value);
        } elseif ($value instanceof UnitEnum) {
            $value = static::getEnumIndex($value);
        }

        if ($value instanceof Stringable) {
            $value = (string)$value;
        }

        if (
            is_numeric($value) ||
            is_bool($value)
        ) {
            return (int)$value;
        }

        return null;
    }

    protected static function getEnumIndex(
        UnitEnum $enum
    ): int {
        foreach ($enum::cases() as $i => $case) {
            if ($case === $enum) {
                return $i;
            }
        }

        throw Exceptional::InvalidArgument(
            message: 'Enum case not found'
        );
    }

    /**
     * Force value to be int
     */
    public static function toInt(
        mixed $value
    ): int {
        return static::tryInt($value) ?? 0;
    }


    /**
     * Ensure value is int between min and max range
     */
    public static function clampInt(
        mixed $value,
        ?int $min = null,
        ?int $max = null
    ): ?int {
        if ($value === null) {
            return null;
        }

        $value = static::asInt($value);

        if ($max !== null) {
            $value = min($max, $value);
        }

        if ($min !== null) {
            $value = max($min, $value);
        }

        return $value;
    }





    /**
     * Coerce value to float
     */
    public static function asFloat(
        mixed $value
    ): float {
        if (null === ($value = static::tryFloat($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to float'
            );
        }

        return $value;
    }

    /**
     * Coerce value to float or null
     */
    public static function tryFloat(
        mixed $value
    ): ?float {
        if ($value instanceof Stringable) {
            $value = (string)$value;
        }

        if (
            is_numeric($value) ||
            is_bool($value)
        ) {
            return (float)$value;
        }

        return null;
    }

    /**
     * Force value to be float
     */
    public static function toFloat(
        mixed $value
    ): float {
        return static::tryFloat($value) ?? 0.0;
    }


    /**
     * Ensure value is float between min and max range
     */
    public static function clampFloat(
        mixed $value,
        ?float $min = null,
        ?float $max = null
    ): ?float {
        if ($value === null) {
            return null;
        }

        $value = static::asFloat($value);

        if ($max !== null) {
            $value = min($max, $value);
        }

        if ($min !== null) {
            $value = max($min, $value);
        }

        return $value;
    }


    /**
     * Ensure value is float in 360 degree range
     */
    public static function clampDegrees(
        mixed $value,
        ?float $min = null,
        ?float $max = null
    ): ?float {
        if ($value === null) {
            return null;
        }

        $value = static::asFloat($value);

        while ($value < 0) {
            $value += 360;
        }

        while ($value > 359) {
            $value -= 360;
        }

        if ($min !== null) {
            $value = max($min, $value);
        }

        if ($max !== null) {
            $value = min($max, $value);
        }

        return $value;
    }



    /**
     * Coerce value to array
     *
     * @template TKey of int|string
     * @template TValue
     * @param iterable<TKey,TValue>|Closure():(Generator<TKey,TValue>)|mixed $value
     * @return ($value is iterable ? array<TKey,TValue> : array<mixed>)
     */
    public static function asArray(
        mixed $value
    ): array {
        if (null === ($value = static::tryArray($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to array'
            );
        }

        return $value;
    }

    /**
     * Coerce value to array or null
     *
     * @template TKey of int|string
     * @template TValue
     * @param iterable<TKey,TValue>|Closure():(Generator<TKey,TValue>)|mixed $value
     * @return ($value is iterable ? array<TKey,TValue> : ?array<mixed>)
     */
    public static function tryArray(
        mixed $value
    ): ?array {
        if($value instanceof Closure) {
            $ref = new ReflectionFunction($value);

            if(
                $ref->isGenerator() &&
                count($ref->getParameters()) == 0
            ) {
                $value = $value();
            }
        }

        if (
            is_array($value) ||
            $value instanceof stdClass
        ) {
            return (array)$value;
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value);
        }

        return null;
    }

    /**
     * Force array value
     *
     * @template TKey of int|string
     * @template TValue
     * @param iterable<TKey,TValue>|Closure():(Generator<TKey,TValue>)|mixed $value
     * @return ($value is iterable ? array<TKey,TValue> : array<mixed>)
     */
    public static function toArray(
        mixed $value
    ): array {
        if(null !== ($output = static::tryArray($value))) {
            return $output;
        }

        if ($value === null) {
            return [];
        }

        return [$value];
    }





    /**
     * Coerce value to iterable
     *
     * @template TKey of int|string
     * @template TValue
     * @param iterable<TKey,TValue>|Closure():(Generator<TKey,TValue>)|mixed $value
     * @return ($value is iterable<TKey,TValue> ? iterable<TKey,TValue> : iterable<mixed>)
     */
    public static function asIterable(
        mixed $value
    ): iterable {
        if (null === ($value = static::tryIterable($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to iterable'
            );
        }

        return $value;
    }

    /**
     * Coerce value to iterable or null
     *
     * @template TKey of int|string
     * @template TValue
     * @param iterable<TKey,TValue>|Closure():(Generator<TKey,TValue>)|mixed $value
     * @return ($value is iterable<TKey,TValue> ? iterable<TKey,TValue> : ?iterable<mixed>)
     */
    public static function tryIterable(
        mixed $value
    ): ?iterable {
        if($value instanceof Closure) {
            $ref = new ReflectionFunction($value);

            if(
                $ref->isGenerator() &&
                count($ref->getParameters()) == 0
            ) {
                $value = $value();
            }
        }

        if (
            is_array($value) ||
            $value instanceof stdClass
        ) {
            return (array)$value;
        }

        if ($value instanceof Traversable) {
            return $value;
        }

        return null;
    }

    /**
     * Force iterable value
     *
     * @template TKey of int|string
     * @template TValue
     * @param iterable<TKey,TValue>|Closure():(Generator<TKey,TValue>)|mixed $value
     * @return ($value is iterable<TKey,TValue> ? iterable<TKey,TValue> : iterable<mixed>)
     */
    public static function toIterable(
        mixed $value
    ): iterable {
        if(null !== ($output = static::tryIterable($value))) {
            return $output;
        }

        if ($value === null) {
            return [];
        }

        return [$value];
    }

    /**
     * Iterable to array
     *
     * @template TKey of int|string
     * @template TValue
     * @param iterable<TKey,TValue>|Closure():(Generator<TKey,TValue>) $value
     * @return array<TKey,TValue>
     */
    public static function iterableToArray(
        iterable|Closure $value
    ): array {
        if($value instanceof Closure) {
            $ref = new ReflectionFunction($value);

            if(
                $ref->isGenerator() &&
                count($ref->getParameters()) == 0
            ) {
                $value = $value();
            } else {
                throw Exceptional::InvalidArgument(
                    message: 'Closure must be a generator'
                );
            }
        }

        if (!is_array($value)) {
            $value = iterator_to_array($value);
        }

        return $value;
    }



    /**
     * Coerce to object
     *
     * @template T of object
     * @return ($value is object ? T : object)
     */
    public static function asObject(
        mixed $value
    ): object {
        if (null === ($value = static::tryStdClass($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to stdClass'
            );
        }

        return $value;
    }

    /**
     * Coerce to object or null
     *
     * @template T of object
     * @return ($value is object ? T : ?object)
     */
    public static function tryObject(
        mixed $value
    ): ?object {
        if(is_object($value)) {
            return $value;
        }

        return static::tryStdClass($value);
    }

    /**
     * Force value to be stdClass
     *
     * @template T of object
     * @return ($value is object ? T : object)
     */
    public static function toObject(
        mixed $value
    ): object {
        return static::tryObject($value) ?? new stdClass();
    }



    /**
     * Coerce to stdClass
     */
    public static function asStdClass(
        mixed $value
    ): stdClass {
        if (null === ($value = static::tryStdClass($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to stdClass'
            );
        }

        return $value;
    }

    /**
     * Coerce to stdClass or null
     */
    public static function tryStdClass(
        mixed $value
    ): ?stdClass {
        if ($value instanceof stdClass) {
            return $value;
        }

        if(is_object($value)) {
            $ref = new ReflectionClass($value);
            $output = new stdClass();

            foreach ($ref->getProperties() as $prop) {
                $output->{$prop->getName()} = $prop->getValue($value);
            }

            return $output;
        }

        if (null !== ($output = static::tryArray($value))) {
            return (object)$output;
        }

        return null;
    }

    /**
     * Force value to be stdClass
     */
    public static function toStdClass(
        mixed $value
    ): stdClass {
        return static::tryStdClass($value) ?? new stdClass();
    }




    /**
     * Coerce value to type
     *
     * @template T of object
     * @param class-string<T> $type
     * @return T
     */
    public static function asType(
        mixed $value,
        string $type
    ): object {
        if (null === ($value = static::tryType($value, $type))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to ' . $type
            );
        }

        return $value;
    }

    /**
     * Coerce value to type or null
     *
     * @template T of object
     * @param class-string<T> $type
     * @return T|null
     */
    public static function tryType(
        mixed $value,
        string $type
    ): ?object {
        if (!$value instanceof $type) {
            return null;
        }

        return $value;
    }




    /**
     * Create lazy ghost object
     *
     * @template T of object
     * @param class-string<T> $type
     * @param callable(T): void $initializer
     * @return T
     */
    public static function newLazyGhost(
        string $type,
        callable $initializer
    ): object {
        $ref = new ReflectionClass($type);
        return $ref->newLazyGhost($initializer);
    }

    /**
     * Create lazy proxy object
     *
     * @template T of object
     * @param class-string<T> $type
     * @param callable(T): T $factory
     * @return T
     */
    public static function newLazyProxy(
        string $type,
        callable $factory
    ): object {
        $ref = new ReflectionClass($type);
        return $ref->newLazyProxy($factory);
    }




    /**
     * Coerce value to DateTime
     *
     * @template T of mixed
     * @param T $value
     * @return (T is DateTimeInterface ? T : DateTime)
     */
    public static function asDateTime(
        mixed $value
    ): DateTimeInterface {
        if (null === ($value = static::tryDateTime($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to DateTime'
            );
        }

        return $value;
    }

    /**
     * Coerce value to DateTime
     *
     * @template T of mixed
     * @param T $value
     * @return (T is DateTimeInterface ? T : ?DateTime)
     */
    public static function tryDateTime(
        mixed $value
    ): ?DateTimeInterface {
        if ($value === null) {
            return null;
        } elseif ($value instanceof DateTimeInterface) {
            return $value;
        }

        if ($value instanceof DateInterval) {
            $now = new DateTime('now');
            return $now->add($value);
        }

        $timestamp = null;

        if (is_numeric($value)) {
            $timestamp = $value;
            $value = 'now';
        } elseif (null === ($value = static::tryString($value))) {
            return null;
        }

        $value = new DateTime($value);

        if ($timestamp !== null) {
            $value->setTimestamp((int)$timestamp);
        }

        return $value;
    }

    /**
     * Coerce value to DateTime
     *
     * @template T of mixed
     * @param T $value
     * @return (T is DateTimeInterface ? T : DateTime)
     */
    public static function toDateTime(
        mixed $value
    ): DateTimeInterface {
        return static::tryDateTime($value) ?? new DateTime('now');
    }




    /**
     * Coerce value to DateInterval
     *
     * @template T of mixed
     * @param T $value
     * @return (T is DateInterval ? T : DateInterval)
     */
    public static function asDateInterval(
        mixed $value
    ): DateInterval {
        if (null === ($value = static::tryDateInterval($value))) {
            throw Exceptional::InvalidArgument(
                message: 'Value could not be coerced to DateInterval'
            );
        }

        return $value;
    }

    /**
     * Coerce value to DateInterval or null
     *
     * @template T of mixed
     * @param T $value
     * @return (T is DateInterval ? T : ?DateInterval)
     */
    public static function tryDateInterval(
        mixed $value
    ): ?DateInterval {
        if ($value === null) {
            return null;
        } elseif ($value instanceof DateInterval) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->diff(new DateTime('now'));
        }

        if (is_float($value)) {
            $value = (int)$value;
        }

        if (is_int($value)) {
            if ($value < time() / 10) {
                if (false === ($output = DateInterval::createFromDateString((string)$value . ' seconds'))) {
                    throw Exceptional::InvalidArgument(
                        message: 'DateInterval value could not be parsed'
                    );
                }

                return $output;
            }

            $value = static::asDateTime($value);
            return $value->diff(new DateTime('now'));
        }

        if (null === ($value = static::tryString($value))) {
            return null;
        }

        if (false === strpos($value, ' ')) {
            try {
                return new DateInterval($value);
            } catch (Exception $e) {
            }
        }

        if (false === ($output = DateInterval::createFromDateString($value))) {
            throw Exceptional::InvalidArgument(
                message: 'DateInterval value could not be parsed'
            );
        }

        return $output;
    }
}
