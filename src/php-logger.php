<?php

class php_logger
{
    public const DEFAULT = "default";
    public const ALL = "all";
    public const NONE = "none";

    public static $call_source = true;
    public static $timestamp = false;
    public static $nanos = true;
    public static $prefix = "\n<br/>";
    public static $suffix = "";

    public static $line_numbers = true;
    public static $scan = false;
    public static $nocrlf = false;
    public static $truncate = 0;

    public static $headling_prefix = "#############################################################\n###\n###";
    public static $alert_prefix = "##################################\n##################################\n";
    public static $error_prefix = "**********************************";
    public static $warning_prefix = "********";
    public static $note_prefix = "***";
    public static $call_prefix = "===> CALL:";
    public static $result_prefix = "<=== RESULT:";
    
    protected static $default_level = "warning";
    protected static $levels = [];

    public static $disable = false;
    public static $suppress_output = false;

    public static $last_message = "";
    public static $count = 0;

    public static $log_folder = __DIR__;
    public static $log_file = null;
    public static $log_max_size = 0;

    public static function reset() {
        self::$call_source = true;
        self::$timestamp = false;
        self::$nanos = false;

        self::$prefix = "\n<br/>";
        self::$line_numbers = true;
        self::$scan = false;
        self::$nocrlf = false;
        self::$truncate = 0;

        self::$headling_prefix = "#############################################################\n###\n###";
        self::$alert_prefix = "##################################\n##################################\n";
        self::$error_prefix = "**********************************";
        self::$warning_prefix = "******";
        self::$note_prefix = "***";
        self::$call_prefix = "===> CALL:";
        self::$result_prefix = "<=== RESULT:";

        self::$default_level = "warning";
        self::$levels = [];

        self::$suppress_output = false;
        self::$disable = false;

        self::$last_message = "";
        self::$count = 0;

        self::$log_file = null;
    }

    protected static function source($next = false, $reset = false) {
        static $trace;
        if ($reset) $trace = debug_backtrace();
        for($i = 0; $i < sizeof($trace); $i++) {
            if (array_key_exists('class', $trace[$i]) && @$trace[$i]['class'] == get_class()) continue;
            if (self::is_log_type($trace[$i]['function'])) continue;  // Ignore shadowed methods
            return !$next ? $trace[$i] : $trace[$i - 1];
        }
        return null;
    }

    protected static function source_function() { return array_key_exists('function', $s = self::source(false, true)) ? $s['function'] : null; }
    protected static function source_class() { return array_key_exists('class', $s = self::source()) ? $s['class'] : basename($s['file']); }
    protected static function source_line() { return array_key_exists('line', $s = self::source(true)) ? $s['line'] : 0; }
    protected static function source_args() { return array_key_exists('args', $s = self::source()) ? $s['args'] : []; }

    static function enable($level = 'all') {
        self::set_log_level(self::source_class() . '::' . self::source_function(), $level);
    }
    static function disble() { self::enable('none'); }

    static function clear_log_levels($deflev = '#') {
        self::$levels = [];
        if ($deflev != '#') {
            if (!in_array($deflev, self::log_types()))
                throw new Exception("Invalid default level in clear_log_levels.  Got [$deflev], expected one of [".join(',', self::log_types())."].");
            self::$default_level = $deflev;
        }
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
            'warn' => 4,
            'note' => 5,
            'call' => 6,
            'result' => 6,
            'info' => 6,
            'log' => 7,
            'debug' => 8,
            'trace' => 9,
            'dump' => 10,
            'temp' => 11,
            'scan' => 126,
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
        if ($chk == self::log_level_values("scan")) return true;
        if (!self::is_log_type($chk) || !self::is_log_type($limit))
            return false;
        return self::log_type_level($chk) <= self::log_type_level($limit);
    }

    protected static function msg($level, $msgs) {
        if (self::$disable) return;
        $fn = self::source_function();
        $cl = self::source_class();

        $src = $cl;
        $slv = self::get_log_level($src);
        
        $sfn = "$cl::$fn";
        $sfl = self::get_log_level($sfn);

        if (!self::log_level_displayed($level, $slv) &&
            !self::log_level_displayed($level, $sfl))
            return false;

        $out = "";
        if (self::$timestamp) $out .= date("H:i:s");
        if (self::$nanos) $out .= (self::$timestamp ? "." : "") . sprintf("%06d", gettimeofday()["usec"]);
        if (self::$timestamp || self::$nanos) $out .= " - ";
        $out .= strtoupper(strrev(substr(strrev("        $level"), 0, 7))) . ": ";
        if (self::$call_source || self::$timestamp) {
            $out .= "[";
            if (self::$call_source) $out .= self::source_class() . "::" . self::source_function();
            if (self::$line_numbers) {
                $lineno = self::source_line();
                if ($lineno != 0) $out .= ":$lineno";
            }
            $out .= "]: ";
        }
        
        $n = 0;
        foreach($msgs as $m) {
            if ($n++) $out .= " ";
            if (is_string($m)) $out .= $m;
            else {
                $tVal = print_r($m, true);
                if (self::$truncate > 0) $tVal = substr($tVal, 0, self::$truncate);
                $out .= $tVal;
            }
        }

        if (self::$nocrlf) $out = str_replace(["\n", "\r"], "", $out);
        if (!self::$suppress_output) print self::$prefix . $out . self::$suffix;
        if (self::$log_file) self::log_file("$out\n");
        self::$last_message = $out;
        
        self::$count++;
        return true;
    }

    protected static function log_file($out) {
        $f = false === strpos(self::$log_file, DIRECTORY_SEPARATOR) ?
            self::$log_folder . DIRECTORY_SEPARATOR . self::$log_file :
            self::$log_file;
        if (false !== strpos($f, '*')) $f = str_replace('*', self::source_class(), $f);

        if (file_exists(self::$log_file) && filesize(self::$log_file) > self::$log_max_size)
            self::truncate_log($f);

        file_put_contents($f, $out, FILE_APPEND);
    }

    protected static function truncate_log($f) {
        $t = file_get_contents($f);
        $t = strpos($t, strlen($t) * 0.60);
        file_put_contents($f, $t);
    }

    protected static function source_arg_names() {
        $result = [];
        try {
            $method = self::source_class() ?
                new ReflectionMethod(self::source_class(), self::source_function()) :
                new ReflectionFunction(self::source_function());
            
            foreach ($method->getParameters() as $param) 
                $result[] = $param->name;
            return $result;
        } catch (Exception $e) {
            // ignore
        }
        return [];
    }

    protected static function named_and_args() {
        $args = self::source_args();
        $names = self::source_arg_names();
        $ca = count($args);
        $cn = count($names);
        $c = $ca >= $cn ? $ca : $cn;

        $result = [];
        for($i = 0; $i < $c; $i++) {
            $n = $i < $cn ? $names[$i] . "=" : '...=';
            $a = $i < $ca ? $args[$i] : '';
            $result[] = $n;
            $result[] = $a;
        }

        return $result;
    }

    static function headline(...$msgs) { return self::msg("headline", array_merge([self::$headling_prefix], $msgs)); }
    static function alert(...$msgs) { return self::msg("alert", array_merge([self::$alert_prefix], $msgs)); }
    static function error(...$msgs) { return self::msg("error", array_merge([self::$error_prefix], $msgs)); }
    static function warning(...$msgs) { return self::msg("warning", array_merge([self::$warning_prefix], $msgs)); }
    static function warn(...$msgs) { return self::warning(...$msgs); }
    static function note(...$msgs) { return self::msg("note", array_merge([self::$note_prefix], $msgs)); }
    static function info(...$msgs) { return self::msg("info", $msgs); }
    static function log(...$msgs) { return self::msg("log", $msgs); }
    static function debug(...$msgs) { return self::msg("debug", $msgs); }
    static function trace(...$msgs) { return self::msg("trace", $msgs); }
    static function dump(...$msgs) { return self::msg("dump", $msgs); }
    static function temp(...$msgs) { return self::msg("temp", $msgs); }
    static function scan(...$msgs) { return self::msg("scan", $msgs); }

    static function call(...$msgs) { return self::msg("call", array_merge([self::$call_prefix, "[".self::source_function()."]"], $msgs, self::named_and_args())); }
    static function result(...$msgs) { return self::msg("result", array_merge([self::$result_prefix, "[".self::source_function()."]"], $msgs)); }
}
