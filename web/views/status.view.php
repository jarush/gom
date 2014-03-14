<?php

$gomHost = '127.0.0.1';
$gomPort = 6965;

$app->get('/', $authMw, function () use($app, $gomHost, $gomPort) {
  $gomClient = null;
  try {
    $gomClient = new GomClient($gomHost, $gomPort);
  } catch (Exception $e) {
    $app->render('status.php', array(
      'site'        => 'Garage',
      'title'       => 'Status',
      'garageDoors' => array(),
      'status'      => 'error',
      'message'     => 'Failed to connect to gomd: ' . $e->getMessage()
    ));
    return;
  }

  $garageDoors = GarageDoor::LoadGarageDoors($gomClient);

  $app->render('status.php', array(
    'site'        => 'Garage',
    'title'       => 'Status',
    'garageDoors' => $garageDoors,
  ));
});

$app->post('/door/status', $authMw, function () use($app, $gomHost, $gomPort) {
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

  $gomClient = new GomClient($gomHost, $gomPort);

  $garageDoors = GarageDoor::LoadGarageDoors($gomClient);
  $status = $garageDoors[$door]->getStatus();

  echo json_encode(array(
    'status'     => 'ok',
    'door'       => $door,
    'doorStatus' => $status,
  ));
});

$app->post('/door/pressbutton', $authMw, function () use($app, $gomHost, $gomPort) {
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

  $gomClient = new GomClient($gomHost, $gomPort);

  $garageDoors = GarageDoor::LoadGarageDoors($gomClient);
  $garageDoors[$door]->pressButton();

  echo json_encode(array(
    'status'   => 'ok',
  ));
});

?>
