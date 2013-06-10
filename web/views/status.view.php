<?php

$gpioHost = '192.168.0.140';
$gpioPort = 6965;

$app->get('/', $authMw, function () use($app, $gpioHost, $gpioPort) {
  $gpioClient = new GpioClient($gpioHost, $gpioPort);
  $garageDoors = GarageDoor::LoadGarageDoors('garagedoors.properties', $gpioClient);

  $app->render('status.php', array(
    'site'    => 'Garage',
    'title'   => 'Status',
    'garageDoors' => $garageDoors,
  ));
});

$app->post('/door/status', $authMw, function () use($app, $gpioHost, $gpioPort) {
  $req = $app->request();

  $door = $req->post('door');
  if (!isset($door)) {
    echo json_encode(array(
      'status'  => 'error',
      'message' => 'Missing required door parameter',
    ));
    $app->response()->status(500);
    return;
  }

  $gpioClient = new GpioClient($gpioHost, $gpioPort);

  $garageDoors = GarageDoor::LoadGarageDoors('garagedoors.properties', $gpioClient);
  $status = $garageDoors[$door]->getStatus();

  echo json_encode(array(
    'status'     => 'ok',
    'door'       => $door,
    'doorStatus' => $status,
  ));
});

$app->post('/door/pressbutton', $authMw, function () use($app, $gpioHost, $gpioPort) {
  $req = $app->request();

  $door = $req->post('door');
  if (!isset($door)) {
    echo json_encode(array(
      'status'  => 'error',
      'message' => 'Missing required door parameter',
    ));
    $app->response()->status(500);
    return;
  }

  $gpioClient = new GpioClient($gpioHost, $gpioPort);

  $garageDoors = GarageDoor::LoadGarageDoors('garagedoors.properties', $gpioClient);
  $garageDoors[$door]->pressButton();

  echo json_encode(array(
    'status'   => 'ok',
  ));
});

?>
