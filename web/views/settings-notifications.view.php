<?php

$app->get('/settings/notifications', $authMw, function () use($app) {
  $app->render('settings-notifications.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Notifications',
  ));
});

$app->post('/settings/notifications/boxcar_subscribe', $authMw, function() use($app) {
  $req = $app->request();

  $email = $req->post('email');
  if (!isset($email)) {
    echo json_encode(array(
      'status'  => 'error',
      'message' => 'Missing required email parameter',
    ));
    $app->response()->status(500);
    return;
  }

  $config = new Config('boxcar.properties');
  $url = 'http://boxcar.io/devices/providers/'
    . $config->get('providerKey', null)
    . '/notifications/subscribe';

  $postFields = http_build_query(array(
    'email' => $email,
  ));

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
  $response = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);

  echo json_encode(array(
    'status'   => 'ok',
    'info'     => $info,
  ));
});

$app->post('/settings/notifications/boxcar_test', $authMw, function() use($app) {
  $req = $app->request();

  $email = $req->post('email');
  if (!isset($email)) {
    echo json_encode(array(
      'status'  => 'error',
      'message' => 'Missing required email parameter',
    ));
    $app->response()->status(500);
    return;
  }

  $config = new Config('boxcar.properties');
  $url = 'http://boxcar.io/devices/providers/'
    . $config->get('providerKey', null)
    . '/notifications';

  $postFields = http_build_query(array(
    'email'                          => $email,
    'notification[from_screen_name]' => 'Test',
    'notification[message]'          => 'This is a test',
  ));

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
  $response = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);

  echo json_encode(array(
    'status'   => 'ok',
    'info'     => $info,
  ));
});


?>
