<?php

$app->get('/settings/garagedoors', $authMw, function () use($app) {
  $garageDoors = GarageDoor::LoadGarageDoors(null);

  $app->render('settings-garagedoors.php', array(
    'site'        => 'Garage',
    'title'       => 'Settings',
    'tab'         => 'Garage Doors',
  ));
});

$app->post('/settings/garagedoors', $authMw, function () use($app) {
  $c = new Config('gomd.properties');

  $req = $app->request();
  $enabled = $req->post('enabled');
  $name = $req->post('name');
  $sensorGpio = $req->post('sensorGpio');
  $relayGpio = $req->post('relayGpio');

  $n = count($name);
  for ($door = 0; $door < $n; $door++) {
    $prefix = 'door' . $door;

    if (isset($enabled[$door])) {
      $c->set($prefix . '.enabled', $enabled[$door]);
    }

    if (isset($name[$door])) {
      $c->set($prefix . '.name', $name[$door]);
    }

    if (isset($sensorGpio[$door])) {
      $c->set($prefix . '.sensor.gpio', $sensorGpio[$door]);
      $c->set($prefix . '.sensor.active_low', 1);
      $c->set($prefix . '.sensor.interval.sec', 0);
      $c->set($prefix . '.sensor.interval.nsec', 500000000);
      $c->set($prefix . '.sensor.notification_delay', 10.0);
    }

    if (isset($relayGpio[$door])) {
      $c->set($prefix . '.relay.gpio', $relayGpio[$door]);
    }
  }

  if ($c->save('gomd.properties') === FALSE) {
    $app->render('settings-garagedoors.php', array(
      'site'    => 'Garage',
      'title'   => 'Settings',
      'tab'     => 'Garage Doors',
      'status'  => 'error',
      'message' => 'Failed to save garage door settings',
    ));
    return;
  }

  $app->render('settings-garagedoors.php', array(
    'site'    => 'Garage',
    'title'   => 'Settings',
    'tab'     => 'Garage Doors',
    'status'  => 'success',
    'message' => 'Settings saved successfully',
  ));
});

?>
