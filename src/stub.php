<?php
spl_autoload_register(function ($name) {
    $d = (strpos(__FILE__, ".phar") === false ? __DIR__ : "phar://" . __FILE__ . "/src");
    if ($name == "php_logger") require_once($d . "/php-logger.php");
});

__HALT_COMPILER();