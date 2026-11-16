<?php

/**
 * Coercion
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Coercion\Tests\NullArrayKeys;

require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

function expectSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new \RuntimeException('Unexpected null-key regression result: ' . var_export($actual, true));
    }
}

try {
    /** @return \Generator<int|string|null,string|null> */
    function keyedValues(): \Generator
    {
        yield null => 'first';
        yield '' => 'empty';
        yield '0' => 'first zero';
        yield 0 => 'zero';
        yield 'named' => null;
    }

    $expected = ['' => 'empty', 0 => 'zero', 'named' => null];
    foreach (['tryArray', 'asArray', 'toArray', 'iterableToArray'] as $method) {
        // Reflection deliberately exercises native inputs beyond the PHPDoc key bound.
        $reflection = new \ReflectionMethod(\DecodeLabs\Coercion::class, $method);
        expectSame($expected, $reflection->invoke(null, keyedValues()));
        expectSame($expected, $reflection->invoke(null, keyedValues(...)));
        expectSame([], $reflection->invoke(null, []));
        expectSame(['' => null, 0 => 'zero'], $reflection->invoke(null, ['' => null, 0 => 'zero']));
    }
    expectSame(null, \DecodeLabs\Coercion::tryArray(null));
    expectSame([], \DecodeLabs\Coercion::toArray(null));
} finally {
    restore_error_handler();
}

echo "Null array-key regression checks passed.\n";
