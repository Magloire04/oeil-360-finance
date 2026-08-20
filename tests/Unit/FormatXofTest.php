<?php

namespace Tests\Unit;

use Tests\TestCase;

class FormatXofTest extends TestCase
{
    public function test_formats_a_standard_amount_with_thousands_separator(): void
    {
        $this->assertSame('1 250 000 XOF', formatXof(1250000));
    }

    public function test_formats_zero(): void
    {
        $this->assertSame('0 XOF', formatXof(0));
    }

    public function test_drops_and_rounds_decimals(): void
    {
        $this->assertSame('50 000 XOF', formatXof(50000.00));
        $this->assertSame('1 500 XOF', formatXof(1499.6));
    }
}
