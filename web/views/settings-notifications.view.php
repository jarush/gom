<?php

$app->get('/settings/notifications', $authMw, function () use($app) {
  $app->render('settings-notifications.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Notifications',
  ));
});

$app->get('/test', function () use($app) {
  phpinfo();
});

$app->post('/settings/notifications', $authMw, function() use($app) {
  $config = new Config('notifications.properties');
  $req = $app->request();

  $boxcarAccessToken = $req->post('boxcar_access_token');
  if (isset($boxcarAccessToken)) {
    $config->set('boxcar_access_token', $boxcarAccessToken);
  }

  $boxcarSound = $req->post('boxcar_sound');
  if (isset($boxcarSound)) {
    $config->set('boxcar_sound', $boxcarSound);
  }

  if ($config->save('notifications.properties') === FALSE) {
    $app->render('settings-notifications.php', array(
      'site'    => 'Garage',
      'title'   => 'Settings',
      'tab'     => 'Notifications',
      'status'  => 'error',
      'message' => 'Failed to save settings',
    ));
    return;
  }

  $app->render('settings-notifications.php', array(
    'site'    => 'Garage',
    'title'   => 'Settings',
    'tab'     => 'Notifications',
    'status'  => 'success',
    'message' => 'Settings saved successfully',
  ));
});

$app->post('/settings/notifications/boxcar_test', $authMw, function() use($app) {
  $req = $app->request();

  $accessToken = $req->post('access_token');
  $sound = $req->post('sound');
  if (!isset($accessToken) || !isset($sound)) {
    echo json_encode(array(
      'status'  => 'error',
      'message' => 'Missing required parameter(s)',
    ));
    $app->response()->status(500);
    return;
  }

  $url = 'https://new.boxcar.io/api/notifications';

  $postFields = http_build_query(array(
    'user_credentials'           => $accessToken,
    'notification[title]'        => 'Test',
    'notification[long_message]' => 'This is a test',
    'notification[sound]'        => $sound,
  ));

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  $response = curl_exec($ch);
  $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($statusCode < 200 || $statusCode > 299) {
    $obj = json_decode($response);

    $message = null;
    if (isset($obj->Response)) {
      $message = 'Boxcar replied - ' . $obj->Response;
    } else {
      $message = 'Failed to send message to Boxcar';
    }

    echo json_encode(array(
      'status'  => 'error',
      'message' => $message,
    ));
    $app->response()->status(500);
    return;
  }

  echo json_encode(array(
    'status'  => 'success',
    'message' => 'Test message sent',
  ));
});

?>
