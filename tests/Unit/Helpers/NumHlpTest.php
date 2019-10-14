<?php

namespace Tests\Unit\Helpers;

use Facades\App\Helpers\NumHlp;
use Tests\TestCase;

class NumHlpTest extends TestCase
{
    /**
     * @test
     * @group u-helper
     * @dataProvider readableInt
     */
    public function number_format_short($expected, $actual)
    {
        $this->assertSame($expected, number_format_short($actual));

        $int_or_str = number_format_short($actual);

        if (is_int($int_or_str)) {
            $this->assertIsInt($int_or_str);
        } else {
            $this->assertIsString($int_or_str);
        }
    }

    public function readableInt()
    {
        return [
            ['12', 12],
            ['12', 12.3],

            ['1K', pow(10, 3)],
            ['10K', pow(10, 4)],
            ['100K', pow(10, 5)],

            ['1M', pow(10, 6)],
            ['10M', pow(10, 7)],
            ['100M', pow(10, 8)],

            ['1B', pow(10, 9)],
            ['10B', pow(10, 10)],
            ['100B', pow(10, 11)],

            ['1T', pow(10, 12)],
            ['10T', pow(10, 13)],
            ['100T', pow(10, 14)],

            ['12K+', 12345],
            ['12M+', 12345678],
            ['1B+', 1234567890],
            ['1T+', 1234567890000],
        ];
    }

    /**
     * @test
     * @group u-helper
     * @dataProvider numberFormatPrecision
     */
    public function number_format_precision($expected, $actual, $precision = 2)
    {
        $this->assertSame($expected, NumHlp::number_format_precision($actual, $precision));
    }

    public function numberFormatPrecision()
    {
        return [
            [10, 10.0],
            [10, 10.00],
            [10.11, 10.111],
            [10.127, 10.1279, 3],
        ];
    }
}
