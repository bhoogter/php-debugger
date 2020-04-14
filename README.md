# php-logger

##

Conditionally logs output (to stdout) based on the logging levels configured, either globally or per-calling class.

## Usage

php_logger::error('msg');
php_logger::warning('msg');
php_logger::info('msg');
php_logger::log('msg');
php_logger::debug('msg');
php_logger::trace('msg');

## Config

php_logger::defaultLevel = 'warning';
php_logger::set_log_level($stringClassName, $stringLevel);
