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
  $config = new Config('gomd.properties');
  $req = $app->request();

  $boxcarEnabled = $req->post('boxcarEnabled');
  if (isset($boxcarEnabled)) {
    $config->set('boxcar.enabled', $boxcarEnabled);
  }

  $boxcarAccessToken = $req->post('boxcarAccessToken');
  if (isset($boxcarAccessToken)) {
    $config->set('boxcar.access_token', $boxcarAccessToken);
  }

  $boxcarSound = $req->post('boxcarSound');
  if (isset($boxcarSound)) {
    $config->set('boxcar.sound', $boxcarSound);
  }

  $emailEnabled = $req->post('emailEnabled');
  if (isset($emailEnabled)) {
    $config->set('email.enabled', $emailEnabled);
  }

  $emailUrl = $req->post('emailUrl');
  if (isset($emailUrl)) {
    $config->set('email.url', $emailUrl);
  }

  $emailUsername = $req->post('emailUsername');
  if (isset($emailUsername)) {
    $config->set('email.username', $emailUsername);
  }

  $emailPassword = $req->post('emailPassword');
  if (isset($emailPassword)) {
    $config->set('email.password', $emailPassword);
  }

  $emailFrom = $req->post('emailFrom');
  if (isset($emailFrom)) {
    $config->set('email.from', $emailFrom);
  }

  $emailTo = $req->post('emailTo');
  if (isset($emailTo)) {
    $config->set('email.to', $emailTo);
  }

  if ($config->save('gomd.properties') === FALSE) {
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

  $boxcarAccessToken = $req->post('boxcarAccessToken');
  $boxcarSound = $req->post('boxcarSound');
  if (!isset($boxcarAccessToken) || !isset($boxcarSound)) {
    echo json_encode(array(
      'status'  => 'error',
      'message' => 'Missing required parameter(s)',
    ));
    $app->response()->status(500);
    return;
  }

  $url = 'https://new.boxcar.io/api/notifications';

  $postFields = http_build_query(array(
    'user_credentials'           => $boxcarAccessToken,
    'notification[title]'        => 'Test',
    'notification[long_message]' => 'This is a test',
    'notification[sound]'        => $boxcarSound,
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
