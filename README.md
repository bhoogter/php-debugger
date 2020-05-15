# php-logger

##

Conditionally logs output (to stdout) based on the logging levels configured, either globally or per-calling class.

## Usage

```
php_logger::error('msg');
php_logger::warning('msg');
php_logger::info('msg');
php_logger::log('msg');
php_logger::debug('msg');
php_logger::trace('msg');
```

## Config

```
php_logger::default_level = 'warning';
php_logger::set_log_level($stringClassName, $stringLevel);
php_logger::suppress_output = true;
```

## Options

- `php_logger::reset()` - Reset 'factory' defaults

- `php_logger::$call_source` (__true__/false) - Show class/function on log
- `php_logger::$timestamp` (true/__false__) - Show timestamp on log
- `php_logger::$prefix` - (String - __`\n<br/>`__) - Prepend every line with this
- `php_logger::$suffix` - (String - __``__) - Append every line with this

- `php_logger::$line_numbers` (__true__/false) - Show line numbers
- `php_logger::$scan` (true/__false__) - Show `scan` logs regardless of setting.
- `php_logger::$nocrlf` (true/__false__) - Remove all \n or \r (from message, not prefix/suffic/et al)
- `php_logger::$truncate` (number - 0) - Limit each log line to this many characters (0 == no limit)

- `php_logger::$heading_prefix` - Special prefix for `heading` log
- `php_logger::$alert_prefix` - Special prefix for `alert` log
- `php_logger::$error_prefix` - Special prefix for `error` log
- `php_logger::$warning_prefix` - Special prefix for `warning` log
- `php_logger::$note_prefix` - Special prefix for `note` log
- `php_logger::$call_prefix` - Special prefix for `call` log
- `php_logger::$result_prefix` - Special prefix for `result` log

- `php_logger::$suppress_output` - (true/__false__) - Suppress output.  Everything happens EXCEPT outputing to stdout.

- `php_logger::$last_message` - String.  Stores last output message.
- `php_logger::$count` - Number.  Number of messages logged.  Does not count messages suppressed by log level.

- `php_logger::$log_folder` - String.  (__null__) - Folder for log backing, if `log_file` is not absolute.
- `php_logger::$log_file` - String.  (__null__) - File for log backing.
- `php_logger::$log_max_size` - String.  (__null__) - File for log backing.
