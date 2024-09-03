<?php

/**
 * @package Coercion
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Coercion\Tests;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use DecodeLabs\Coercion;

class TestDateTime
{
    public function testToDateTime(): Carbon
    {
        $date = new Carbon('2020-01-01');
        return Coercion::toDateTime($date);
    }

    /*
    public function testMixed(): DateTime
    {
        $output = Coercion::toDateTime($this->mixedValue());
        return $output;
    }

    protected function mixedValue(): mixed
    {
        return '2020-01-01';
    }
    */

    public function testToDateInterval(): CarbonInterval
    {
        $date = new CarbonInterval('P1D');
        return Coercion::toDateInterval($date);
    }
}
