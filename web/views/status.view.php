<?php

$app->get('/', $authMw, function () use($app) {
  $gpioClient = new GpioClient('192.168.0.140', 6965);
  $garageDoor = new GarageDoor($gpioClient, 0, 1);

  $app->render('status.php', array(
    'site'    => 'Garage',
    'title'   => 'Status',
    'garageDoor' => $garageDoor,
  ));
});

?>
