<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

function globalFunctionLog() {
    php_logger::warning("test");
}

class global_test extends TestCase
{
    private const TEST_MSG = "testing";

    public static function setUpBeforeClass(): void
    {
        php_logger::reset();
        php_logger::$suppress_output = true;
    }

    public function testGlobalNoClass() {
        globalFunctionLog();
        $this->assertTrue(true);
    }
}
