<?php

// Absolute filesystem path to the application
define('APP_ROOT', realpath(__DIR__));

// Register an autoloader to find and load classes
function autoload($class) {
  require(APP_ROOT . '/lib/' . $class . '.class.php');
}
spl_autoload_register('autoload');

?>
