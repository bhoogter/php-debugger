<?php

class php_logger
{
    public const DEFAULT = "default";
    public const ALL = "all";
    public const NONE = "none";
    public static $prefix = "\n<br/>";
    public static $suffix = "";
    public static $default_level = "warning";
    public static $levels = [];
    public static $suppress_output = false;
    public static $count = 0;

    protected static function source() {
        $trace = debug_backtrace();
        for($i = 0; $i < sizeof($trace); $i++) {
            if ($trace[$i]['class'] == get_class()) continue;
            return $trace[$i];
        }
        return null;
    }

    protected static function source_function() { return self::source()['function']; }
    protected static function source_class() { return self::source()['class']; }

    static function set_log_level($source, $level) {
        if (!is_string($source)) throw new Exception("php_logging::set_log_level - Invalid Argument 1 'source'.  Expected string, got [".gettype($source)."].");
        if (!is_string($level)) throw new Exception("php_logging::set_log_level - Invalid Argument 2 'level'.  Expected string, got [".gettype($source)."].");
        if (!self::is_log_level($level)) throw new Exception("php_logging::set_log_level - Invalid log level [$level].");
        self::$levels[$source] = $level;
    }

    static function get_log_level($source) {
        if (!is_string($source)) throw new Exception("php_logging::set_log_level - Invalid Argument 1 'source'.  Expected string, got [".gettype($source)."].");
        return isset(self::$levels[$source]) ? self::$levels[$source] : self::$default_level;
    }

    protected static function log_level_values() {
        return [
            self::DEFAULT => 2,
            self::NONE => 0,
            'error' => 1,
            'warning' => 2,
            'info' => 3,
            'log' => 4,
            'debug' => 5,
            'trace' => 6,
            self::ALL => 127
        ];
    }

    static function log_levels() { return array_keys(self::log_level_values()); }
    static function is_log_level($level) { return in_array($level, self::log_levels()); }

    protected static function log_level($lvl) {
        $ll = self::log_level_values();
        return !isset($ll[$lvl]) ? $ll[self::DEFAULT] : $ll[$lvl];
    }

    protected static function log_level_displayed($chk, $limit) {
        if (!self::is_log_level($chk) || !self::is_log_level($limit))
            return false;
        return self::log_level($chk) <= self::log_level($limit);
    }

    protected static function msg($level, $msgs) {
        $src = self::source_class();
        $slv = self::get_log_level($src);
        if (!self::log_level_displayed($level, $slv)) return false;

        if (!self::$suppress_output) {
            print self::$prefix;
            foreach($msgs as $m) {
                if (is_string($m)) print $m; 
                else print_r($m);
            }
            print self::$suffix;
        }
        
        self::$count++;
        return true;
    }

    static function warning(...$msgs) { return self::msg("warning", $msgs); }
    static function error(...$msgs) { return self::msg("error", $msgs); }
    static function info(...$msgs) { return self::msg("info", $msgs); }
    static function log(...$msgs) { return self::msg("log", $msgs); }
    static function debug(...$msgs) { return self::msg("debug", $msgs); }
    static function trace(...$msgs) { return self::msg("trace", $msgs); }
}
