<?php

$app->get('/settings/management', $authMw, function () use($app) {
  $app->render('settings-management.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Management',
  ));
});
$app->post('/settings/management', $authMw, function () use($app) {
  $req = $app->request();

  $username = $req->post('username');
  $password1 = $req->post('password1');
  $password2 = $req->post('password2');

  $message = null;
  if (!isset($username) || $username == '') {
    $message = 'Missing required username field';
  } else if (!isset($password1) || $password1 == '' ||
      !isset($password2) || $password2 == '') {
    $message = 'Missing required password field';
  } else if ($password1 != $password2) {
    $message = 'Passwords do not match';
  }

  if ($message != null) {
    $app->render('settings-management.php', array(
      'site'    => 'Garage',
      'title'   => 'Settings',
      'tab'     => 'Garage Doors',
      'status'  => 'error',
      'message' => $message,
    ));
    return;
  }

  $c = new Config('/config/login.properties');
  $c->set('username', $username);
  $c->set('password', $password1);

  if ($c->save('/config/login.properties') === FALSE) {
    $app->render('settings-management.php', array(
      'site'    => 'Garage',
      'title'   => 'Settings',
      'tab'     => 'Garage Doors',
      'status'  => 'error',
      'message' => 'Failed to save garage door settings',
    ));
    return;
  }

  $app->render('settings-management.php', array(
    'site'    => 'Garage',
    'title'   => 'Settings',
    'tab'     => 'Management',
    'status'  => 'success',
    'message' => 'Settings saved successfully',
  ));
});

?>
