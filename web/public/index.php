<?php
require '../vendor/Slim/Slim.php';
\Slim\Slim::registerAutoloader();
require '../initialize.php';

// Start the session
session_cache_limiter(false);
session_start();

// Create the Slim instance
$app = new \Slim\Slim(array(
  'templates.path' => '../templates'
));

// Include the views
require '../views/login.view.php';
require '../views/status.view.php';
require '../views/settings-garagedoors.view.php';
require '../views/settings-notifications.view.php';
require '../views/settings-network.view.php';
require '../views/settings-management.view.php';

$app->run();

?>

