<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class func_test extends TestCase
{
    private const TEST_MSG = "testing";

    public static function setUpBeforeClass(): void
    {
        php_logger::reset();
        php_logger::$suppress_output = true;
    }

    public function msg1()
    {
        return php_logger::log(self::TEST_MSG);
    }

    public function msg2()
    {
        return php_logger::log(self::TEST_MSG);
    }

    public function msg3()
    {
        php_logger::enable();
        return php_logger::log(self::TEST_MSG);
    }

    public function msgx()
    {
        php_logger::enable();
        $x = __LINE__ + 1;
        php_logger::log("check lineno");
        return $x;
    }

    public function testLog(): void
    {
        php_logger::clear_log_levels('none');
        php_logger::set_log_level(get_class() . '::msg1', 'log');

        $this->assertTrue($this->msg1());
        $this->assertFalse($this->msg2());
        $this->assertTrue($this->msg3());
    }

    public function testLineNo(): void
    {
        php_logger::reset();
        php_logger::$suppress_output = false;
        php_logger::$line_numbers = true;

        $x = self::msgx();
        $l = php_logger::$last_message;

        $this->assertTrue(false !== strpos($l, "msgx:$x"));
    }
}
