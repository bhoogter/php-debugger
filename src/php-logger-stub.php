<?php

if (!class_exists("php_logger")) {
	class php_logger {
        public const DEFAULT = "default";
        public const ALL = "all";
        public const NONE = "none";
    
        public static $call_source;
        public static $timestamp;
        public static $nanos;
        public static $prefix;
        public static $suffix;
    
        public static $line_numbers;
        public static $scan;
        public static $nocrlf;
        public static $truncate;
    
        public static $headling_prefix;
        public static $alert_prefix;
        public static $error_prefix;
        public static $warning_prefix;
        public static $note_prefix;
        public static $call_prefix;
        public static $result_prefix;
        
        public static $disable;
        public static $suppress_output;
    
        public static $last_message;
        public static $count;
    
        public static $log_folder;
        public static $log_file;
        public static $log_max_size;
    
        public static function reset() {}
    

        static function set_log_level(...$k) {}
        static function clear_log_levels(...$k) {}
        static function get_log_level($source) {}
        static function log_types() {}

        static function enable(...$k) {}
        static function disable(...$k) {}
        

		static function headline(...$k) {}
		static function alert(...$k) {}
		static function error(...$k) {}
		static function warning(...$k) {}
		static function warn(...$k) {}
		static function note(...$k) {}
		static function info(...$k) {}
		static function log(...$k) {}
		static function debug(...$k) {}
		static function trace(...$k) {}
        static function dump(...$k) {}
        static function temp(...$k) {}
        static function scan(...$k) {}

        static function call(...$k) {}
		static function result(...$k) {}
        }
}