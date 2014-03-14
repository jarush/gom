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
  $name = $req->post('name');
  $sensorGpio = $req->post('sensorGpio');
  $relayGpio = $req->post('relayGpio');

  $n = count($name);
  for ($door = 0; $door < $n; $door++) {
    // Skip the door if any information is missing
    if (!isset($name[$door]) ||
        !isset($sensorGpio[$door]) ||
        !isset($relayGpio[$door])) {
      continue;
    }

    $prefix = 'door' . $door;

    // Configure the garage door
    $c->set($prefix . '.name', $name[$door]);
    $c->set($prefix . '.sensor.gpio', $sensorGpio[$door]);
    $c->set($prefix . '.sensor.active_low', 1);
    $c->set($prefix . '.sensor.interval.sec', 0);
    $c->set($prefix . '.sensor.interval.nsec', 500000000);
    $c->set($prefix . '.sensor.notification_delay', 10.0);
    $c->set($prefix . '.relay.gpio', $relayGpio[$door]);
  }

  while ($door < 10) {
    $prefix = 'door' . $door;

    $c->remove($prefix . '.name');
    $c->remove($prefix . '.sensor.gpio');
    $c->remove($prefix . '.sensor.active_low');
    $c->remove($prefix . '.sensor.interval.sec');
    $c->remove($prefix . '.sensor.interval.nsec');
    $c->remove($prefix . '.sensor.notification_delay');
    $c->remove($prefix . '.relay.gpio');

    $door++;
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
