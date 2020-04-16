<?php

class php_logger
{
    public const DEFAULT = "default";
    public const ALL = "all";
    public const NONE = "none";

    public static $call_source = true;
    public static $timestamp = false;
    public static $prefix = "\n<br/>";
    public static $suffix = "";
    
    public static $default_level = "warning";
    public static $levels = [];

    public static $suppress_output = false;

    public static $last_message = "";
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

    static function clear_log_levels($deflev = 'warning') {
        self::$levels = [];
        self::$default_level = $deflev;
    }

    static function set_log_level($source, $level) {
        if ($source == null) {
            self::$default_level = $level;
        } else {
            if (!is_string($source)) throw new Exception("php_logging::set_log_level - Invalid Argument 1 'source'.  Expected string, got [".gettype($source)."].");
            if (!is_string($level)) throw new Exception("php_logging::set_log_level - Invalid Argument 2 'level'.  Expected string, got [".gettype($source)."].");
            if (!self::is_log_type($level)) throw new Exception("php_logging::set_log_level - Invalid log level [$level].");
            self::$levels[$source] = $level;
        }
        return $level;
    }

    static function get_log_level($source) {
        if (!is_string($source)) throw new Exception("php_logging::set_log_level - Invalid Argument 1 'source'.  Expected string, got [".gettype($source)."].");
        return isset(self::$levels[$source]) ? self::$levels[$source] : self::$default_level;
    }

    protected static function log_level_values() {
        return [
            self::DEFAULT => 2,
            self::NONE => 0,
            'headline' => 1,
            'alert' => 2,
            'error' => 3,
            'warning' => 4,
            'info' => 5,
            'log' => 6,
            'debug' => 7,
            'trace' => 8,
            'dump' => 9,
            'temp' => 10,
            self::ALL => 127
        ];
    }

    static function log_types() { return array_keys(self::log_level_values()); }
    protected static function is_log_type($level) { return in_array($level, self::log_types()); }

    protected static function log_type_level($lvl) {
        $ll = self::log_level_values();
        return !isset($ll[$lvl]) ? $ll[self::DEFAULT] : $ll[$lvl];
    }

    protected static function log_level_displayed($chk, $limit) {
        if (!self::is_log_type($chk) || !self::is_log_type($limit))
            return false;
        return self::log_type_level($chk) <= self::log_type_level($limit);
    }

    protected static function msg($level, $msgs) {
        $src = self::source_class();
        $slv = self::get_log_level($src);
        $sfn = self::source_class() . "::" . self::source_function();
        $sfl = self::get_log_level($sfn);
        if (!self::log_level_displayed($level, $slv) &&
            !self::log_level_displayed($level, $sfl))
            return false;

        $out = "";
        $out .= self::$prefix;
        $out .= strtoupper($level) . ": ";
        if (self::$call_source || self::$timestamp) {
            $out .= "[";
            if (self::$timestamp) $out .= date("H:i:s");
            if (self::$call_source && self::$timestamp) $out .= " - ";
            if (self::$call_source) $out .= self::source_class() . "::" . self::source_function();
            $out .= "]: ";
        }
        
        $n = 0;
        foreach($msgs as $m) {
            if ($n++) $out .= "\t";
            if (is_string($m)) $out .= $m;
            else $out .= print_r($m, true);
        }
        $out .= self::$suffix;

        if (!self::$suppress_output) print $out;
        self::$last_message = $out;
        
        self::$count++;
        return true;
    }

    static function headline(...$msgs) { return self::msg("headline", $msgs); }
    static function alert(...$msgs) { return self::msg("alert", $msgs); }
    static function error(...$msgs) { return self::msg("error", $msgs); }
    static function warning(...$msgs) { return self::msg("warning", $msgs); }
    static function info(...$msgs) { return self::msg("info", $msgs); }
    static function log(...$msgs) { return self::msg("log", $msgs); }
    static function debug(...$msgs) { return self::msg("debug", $msgs); }
    static function trace(...$msgs) { return self::msg("trace", $msgs); }
    static function dump(...$msgs) { return self::msg("dump", $msgs); }
    static function temp(...$msgs) { return self::msg("temp", $msgs); }
}
