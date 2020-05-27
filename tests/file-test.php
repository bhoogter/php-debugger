<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class file_test extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        php_logger::reset();
        php_logger::$suppress_output = true;
    }

    public function testFileOutput(): void {
        $f = __DIR__ . DIRECTORY_SEPARATOR . "tmp.log";
        if (file_exists($f)) @unlink($f);
        $this->assertFalse(@file_get_contents($f));
        php_logger::clear_log_levels('all');
        php_logger::$log_folder = __DIR__;
        php_logger::$log_file = "tmp.log";
        php_logger::warning("Message");
        php_logger::call();
        $result = file_get_contents($f);
        @unlink($f);
        $this->assertNotEquals("", $result);
        print "\n$result";
    }

    public function testFileAppend(): void
    {
        $f = __DIR__ . DIRECTORY_SEPARATOR . "tmp.log";
        if (file_exists($f)) @unlink($f);
        $this->assertFalse(@file_get_contents($f));
        php_logger::clear_log_levels('all');
        php_logger::$log_folder = __DIR__;
        php_logger::$log_file = "tmp.log";

        for($i = 0; $i < 30; $i++) php_logger::log("$i");

        $result = file_get_contents($f);
        $this->assertTrue(substr_count($result, "\n") > 10);
    }
}
