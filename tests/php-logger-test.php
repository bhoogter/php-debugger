<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class php_logger_test extends TestCase
{
	private const TEST_MSG = "testing";

	public function testLog(): void
	{
        php_logger::$defaultLevel = 'warning';
        $this->assertTrue(php_logger::error(self::TEST_MSG));
        $this->assertTrue(php_logger::warning(self::TEST_MSG));
        $this->assertFalse(php_logger::info(self::TEST_MSG));
        $this->assertFalse(php_logger::log(self::TEST_MSG));
        $this->assertFalse(php_logger::debug(self::TEST_MSG));
        $this->assertFalse(php_logger::trace(self::TEST_MSG));
	}

    public function testLogSpecific(): void
	{
        php_logger::$defaultLevel = 'warning';
        php_logger::set_log_level(get_class(), 'info');
        $this->assertEquals('info', php_logger::get_log_level(get_class()));
        $this->assertEquals('warning', php_logger::get_log_level('something else'));
        $this->assertFalse(php_logger::log(self::TEST_MSG));
        $this->assertTrue(php_logger::info(self::TEST_MSG));
	}
}
