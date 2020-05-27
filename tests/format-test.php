<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class format_test extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        php_logger::reset();
        php_logger::$suppress_output = true;
        php_logger::$line_numbers = false;
        php_logger::clear_log_levels('all');
    }

    public function withArgs($a, $bbbb, $c)
    {
        php_logger::call();
    }

    public function doResult($v)
    {
        php_logger::result($v);
    }

    public function testCallFormat(): void {
        $this->withArgs("1", "bBb", 45.6);
        $result = php_logger::$last_message;
        $this->assertEquals(
            "   CALL: [format_test::withArgs]: ===> CALL: [withArgs] a= 1 bbbb= bBb c= 45.6"
            , $result);
    }

    public function testResultFormat(): void {
        $this->doResult("XXX");
        $result = php_logger::$last_message;
        $this->assertEquals(
            " RESULT: [format_test::doResult]: <=== RESULT: [doResult] XXX"
            , $result);
    }

    public function testResultFormatTimestampsNanos(): void {
        php_logger::$timestamp = true;
        php_logger::$nanos = true;
        $this->doResult("XXX");
        $result = php_logger::$last_message;
        print $result;
        $this->assertTrue(!!preg_match("/..:..:......... -  RESULT: \\[format_test::doResult\\]: \\<=== RESULT: \\[doResult\\] XXX/", $result));
    }
}
