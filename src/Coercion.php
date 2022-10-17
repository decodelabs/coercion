<?php

/**
 * @package Coercion
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use Stringable;
use Traversable;

class Coercion
{
    /**
     * Coerce value to string
     */
    public static function toString(mixed $value): string
    {
        if (null === ($value = static::toStringOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to string');
        }

        return $value;
    }

    /**
     * Coerce value to string or null
     */
    public static function toStringOrNull(mixed $value): ?string
    {
        if (
            is_string($value) ||
            $value instanceof Stringable ||
            is_numeric($value)
        ) {
            return (string)$value;
        }

        return null;
    }


    /**
     * Force value to be string
     */
    public static function forceString(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            $output = [];

            foreach ($value as $inner) {
                if (strlen($inner = static::forceString($inner))) {
                    $output[] = $inner;
                }
            }

            return implode(' ', $output);
        }

        return (string)static::toStringOrNull($value);
    }

    /**
     * Is value stringable
     */
    public static function isStringable(mixed $value): bool
    {
        return
            is_string($value) ||
            $value instanceof Stringable ||
            is_numeric($value);
    }


    /**
     * Coerce value to bool
     */
    public static function toBool(mixed $value): bool
    {
        if (null === ($value = static::toBoolOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to bool');
        }

        return $value;
    }

    /**
     * Coerce value to bool or null
     */
    public static function toBoolOrNull(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return (bool)$value;
    }


    /**
     * Coerce value to int
     */
    public static function toInt(mixed $value): int
    {
        if (null === ($value = static::toIntOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to int');
        }

        return $value;
    }

    /**
     * Coerce value to int or null
     */
    public static function toIntOrNull(mixed $value): ?int
    {
        if (is_numeric($value)) {
            return (int)$value;
        }

        return null;
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

        $value = static::toInt($value);

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
    public static function toFloat(mixed $value): float
    {
        if (null === ($value = static::toFloatOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to float');
        }

        return $value;
    }

    /**
     * Coerce value to float or null
     */
    public static function toFloatOrNull(mixed $value): ?float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }

        return null;
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

        $value = static::toFloat($value);

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

        $value = static::toFloat($value);

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
     * @return array<mixed>
     */
    public static function toArray(mixed $value): array
    {
        if (null === ($value = static::toArrayOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to array');
        }

        return $value;
    }

    /**
     * Coerce value to array or null
     *
     * @return array<mixed>|null
     */
    public static function toArrayOrNull(mixed $value): ?array
    {
        if (is_array($value)) {
            return (array)$value;
        }

        if ($value instanceof Traversable) {
            $value = iterator_to_array($value);
        }

        return null;
    }

    /**
     * Iterable to array
     *
     * @template TKey of int|string
     * @template TValue
     * @phpstan-param array<TKey, TValue>|iterable<TKey, TValue> $value
     *
     * @phpstan-return array<TKey, TValue>
     */
    public static function iterableToArray(iterable $value): array
    {
        if (!is_array($value)) {
            $value = iterator_to_array($value);
        }

        return $value;
    }



    /**
     * Coerce value to type
     *
     * @template T of object
     * @phpstan-param class-string<T> $type
     * @phpstan-return T
     */
    public static function toType(
        mixed $value,
        string $type
    ): object {
        if (null === ($value = static::toTypeOrNull($value, $type))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to ' . $type);
        }

        return $value;
    }

    /**
     * Coerce value to type or null
     *
     * @template T of object
     * @phpstan-param class-string<T> $type
     * @phpstan-return T|null
     */
    public static function toTypeOrNull(
        mixed $value,
        string $type
    ): ?object {
        if (!$value instanceof $type) {
            return null;
        }

        return $value;
    }
}
