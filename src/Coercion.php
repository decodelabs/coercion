<?php

/**
 * @package Coercion
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

class Coercion
{
    /**
     * Coerce value to string
     *
     * @param mixed $value
     */
    public static function toString($value): string
    {
        if (null === ($value = static::toStringOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to string');
        }

        return $value;
    }

    /**
     * Coerce value to string or null
     *
     * @param mixed $value
     */
    public static function toStringOrNull($value): ?string
    {
        if (
            is_string($value) ||
            is_numeric($value) ||
            (
                is_object($value) &&
                method_exists($value, '__toString')
            )
        ) {
            return (string)$value;
        }

        return null;
    }


    /**
     * Coerce value to bool
     *
     * @param mixed $value
     */
    public static function toBool($value): bool
    {
        if (null === ($value = static::toBoolOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to bool');
        }

        return $value;
    }

    /**
     * Coerce value to bool or null
     *
     * @param mixed $value
     */
    public static function toBoolOrNull($value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return (bool)$value;
    }


    /**
     * Coerce value to int
     *
     * @param mixed $value
     */
    public static function toInt($value): int
    {
        if (null === ($value = static::toIntOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to int');
        }

        return $value;
    }

    /**
     * Coerce value to int or null
     *
     * @param mixed $value
     */
    public static function toIntOrNull($value): ?int
    {
        if (is_numeric($value)) {
            return (int)$value;
        }

        return null;
    }

    /**
     * Coerce value to float
     *
     * @param mixed $value
     */
    public static function toFloat($value): float
    {
        if (null === ($value = static::toFloatOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to float');
        }

        return $value;
    }

    /**
     * Coerce value to float or null
     *
     * @param mixed $value
     */
    public static function toFloatOrNull($value): ?float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }

        return null;
    }

    /**
     * Coerce value to array
     *
     * @param mixed $value
     * @return array<mixed>
     */
    public static function toArray($value): array
    {
        if (null === ($value = static::toArrayOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to array');
        }

        return $value;
    }

    /**
     * Coerce value to array or null
     *
     * @param mixed $value
     * @return array<mixed>|null
     */
    public static function toArrayOrNull($value): ?array
    {
        if (is_array($value)) {
            return (array)$value;
        }

        return null;
    }


    /**
     * Coerce value to type
     *
     * @template T of object
     * @param mixed $value
     * @param class-string<T> $type
     * @return T
     */
    public static function toType($value, string $type): object
    {
        if (null === ($value = static::toTypeOrNull($value, $type))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to ' . $type);
        }

        return $value;
    }

    /**
     * Coerce value to type or null
     *
     * @template T of object
     * @param mixed $value
     * @param class-string<T> $type
     * @return T|null
     */
    public static function toTypeOrNull($value, string $type): ?object
    {
        if (!$value instanceof $type) {
            return null;
        }

        return $value;
    }
}
