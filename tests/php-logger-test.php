<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class test_object {
    public $field1 = 1;
    public $field2 = "bb";
}

class php_logger_test extends TestCase
{
    private const TEST_MSG = "testing";

    public static function setUpBeforeClass(): void
    {
        php_logger::$suppress_output = true;
    }

    public function msg1() {
        return php_logger::log(self::TEST_MSG);
    }

    public function msg2() {
        return php_logger::log(self::TEST_MSG);
    }


    public function testLog(): void
    {
        php_logger::clear_log_levels();
        $this->assertTrue(php_logger::headline(self::TEST_MSG));
        $this->assertTrue(php_logger::alert(self::TEST_MSG));
        $this->assertTrue(php_logger::error(self::TEST_MSG));
        $this->assertTrue(php_logger::warning(self::TEST_MSG));
        $this->assertFalse(php_logger::info(self::TEST_MSG));
        $this->assertFalse(php_logger::log(self::TEST_MSG));
        $this->assertFalse(php_logger::debug(self::TEST_MSG));
        $this->assertFalse(php_logger::trace(self::TEST_MSG));
        $this->assertFalse(php_logger::dump(self::TEST_MSG));
        $this->assertFalse(php_logger::temp(self::TEST_MSG));
    }

    public function testLogSpecific(): void
    {
        php_logger::clear_log_levels();
        php_logger::set_log_level(get_class(), 'info');
        $this->assertEquals('info', php_logger::get_log_level(get_class()));
        $this->assertEquals('warning', php_logger::get_log_level('something else'));
        $this->assertFalse(php_logger::log(self::TEST_MSG));
        $this->assertTrue(php_logger::info(self::TEST_MSG));
    }

    public function testMultiArg(): void
    {
        php_logger::clear_log_levels();
        $this->assertTrue(php_logger::error(self::TEST_MSG, 1, 45.2, new test_object()));
    }

    public function testExtraOptions(): void
    {
        php_logger::$call_source = true;
        php_logger::$timestamp = true;
        php_logger::$last_message = "";
        php_logger::clear_log_levels();
        $this->assertTrue(php_logger::error(self::TEST_MSG));
        $this->assertTrue(strpos(php_logger::$last_message, " - php_logger_test") !== false);
        $this->assertTrue(strpos(php_logger::$last_message, "testExtraOptions") !== false);
    }

    public function testLogLevelFunction(): void
    {
        php_logger::clear_log_levels();
        php_logger::set_log_level("php_logger_test::msg1", "log");
        php_logger::set_log_level("php_logger_test::msg2", "error");
        $this->assertTrue($this->msg1());
        $this->assertFalse($this->msg2());
    }
}
