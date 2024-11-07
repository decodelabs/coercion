<?php

/**
 * @package Coercion
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use BackedEnum;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use stdClass;
use Stringable;
use Traversable;
use UnitEnum;

class Coercion
{
    /**
     * Coerce value to string
     */
    public static function toString(
        mixed $value
    ): string {
        if (null === ($value = static::toStringOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to string');
        }

        return $value;
    }

    /**
     * Coerce value to string or null
     */
    public static function toStringOrNull(
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
    public static function forceString(
        mixed $value
    ): string {
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
        return (bool)static::toBoolOrNull($value);
    }

    /**
     * Coerce value to bool or null
     */
    public static function toBoolOrNull(
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
    public static function toInt(
        mixed $value
    ): int {
        if (null === ($value = static::toIntOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to int');
        }

        return $value;
    }

    /**
     * Coerce value to int or null
     */
    public static function toIntOrNull(
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

        if (is_numeric($value)) {
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
            'Enum case not found'
        );
    }


    /**
     * Force value to be int
     */
    public static function forceInt(
        mixed $value
    ): int {
        return static::toIntOrNull($value) ?? 0;
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
    public static function toFloat(
        mixed $value
    ): float {
        if (null === ($value = static::toFloatOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to float');
        }

        return $value;
    }

    /**
     * Coerce value to float or null
     */
    public static function toFloatOrNull(
        mixed $value
    ): ?float {
        if ($value instanceof Stringable) {
            $value = (string)$value;
        }

        if (is_numeric($value)) {
            return (float)$value;
        }

        return null;
    }

    /**
     * Force value to be float
     */
    public static function forceFloat(
        mixed $value
    ): float {
        return static::toFloatOrNull($value) ?? 0.0;
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
    public static function toArray(
        mixed $value
    ): array {
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
    public static function toArrayOrNull(
        mixed $value
    ): ?array {
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
     * @return array<mixed>
     */
    public static function forceArray(
        mixed $value
    ): array {
        if (
            is_array($value) ||
            $value instanceof stdClass
        ) {
            return (array)$value;
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value);
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
     * @param array<TKey, TValue>|iterable<TKey, TValue> $value
     * @return array<TKey, TValue>
     */
    public static function iterableToArray(
        iterable $value
    ): array {
        if (!is_array($value)) {
            $value = iterator_to_array($value);
        }

        return $value;
    }


    /**
     * Coerce to stdClass
     */
    public static function toStdClass(
        mixed $value
    ): stdClass {
        if (null === ($value = static::toStdClassOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to stdClass');
        }

        return $value;
    }

    /**
     * Coerce to stdClass or null
     */
    public static function toStdClassOrNull(
        mixed $value
    ): ?stdClass {
        if ($value instanceof stdClass) {
            return $value;
        }

        if (is_array($value)) {
            return (object)$value;
        }

        return null;
    }

    /**
     * Force value to be stdClass
     */
    public static function forceStdClass(
        mixed $value
    ): stdClass {
        return static::toStdClassOrNull($value) ?? new stdClass();
    }



    /**
     * Coerce value to type
     *
     * @template T of object
     * @param class-string<T> $type
     * @return T
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
     * @param class-string<T> $type
     * @return T|null
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
        if (null === ($value = static::toDateTimeOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to DateTime');
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
    public static function toDateTimeOrNull(
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
        } elseif (null === ($value = static::toStringOrNull($value))) {
            return null;
        }

        $value = new DateTime($value);

        if ($timestamp !== null) {
            $value->setTimestamp((int)$timestamp);
        }

        return $value;
    }



    /**
     * Coerce value to DateInterval
     *
     * @template T of mixed
     * @param T $value
     * @return (T is DateInterval ? T : DateInterval)
     */
    public static function toDateInterval(
        mixed $value
    ): DateInterval {
        if (null === ($value = static::toDateIntervalOrNull($value))) {
            throw Exceptional::InvalidArgument('Value could not be coerced to DateInterval');
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
    public static function toDateIntervalOrNull(
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
                        'DateInterval value could not be parsed'
                    );
                }

                return $output;
            }

            $value = static::toDateTime($value);
            return $value->diff(new DateTime('now'));
        }

        if (null === ($value = static::toStringOrNull($value))) {
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
                'DateInterval value could not be parsed'
            );
        }

        return $output;
    }
}
