<?php

$app->get('/settings/garagedoors', $authMw, function () use($app) {
  $app->render('settings-garagedoors.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Garage Doors',
  ));
});

$app->post('/settings/garagedoors', $authMw, function () use($app) {
  $c1 = new Config();
  $c2 = new Config();

  $req = $app->request();
  $name = $req->post('name');
  $sensorGpio = $req->post('sensorGpio');
  $relayGpio = $req->post('relayGpio');

  $n = count($name);

  $door = 0;
  for ($i = 0; $i < $n; $i++) {
    // Skip the door if any information is missing
    if (!isset($name[$i]) || !isset($sensorGpio[$i]) || !isset($relayGpio[$i])) {
      continue;
    }

    // Configure the garage door
    $c1->set('name' . $door, $name[$i]);
    $c1->set('sensorGpio' . $door, $sensorGpio[$i]);
    $c1->set('relayGpio' . $door, $relayGpio[$i]);

    // Configure the sensor gpio
    $gpio = 'gpio' . $sensorGpio[$i];
    $c2->set($gpio . '.direction', 0);
    $c2->set($gpio . '.interval.sec', 0);
    $c2->set($gpio . '.interval.nsec', 500000000);
    $c2->set($gpio . '.active_low', 1);
    $c2->set($gpio . '.action.type', 'print');

    // Configure the relay gpio
    $gpio = 'gpio' . $relayGpio[$i];
    $c2->set($gpio . '.direction', 1);

    $door++;
  }

  if ($c1->save('garagedoors.properties') === FALSE) {
    $app->render('settings-garagedoors.php', array(
      'site'   => 'Garage',
      'title'  => 'Settings',
      'tab'    => 'Garage Doors',
      'status' => 'Failed to save garage door settings',
    ));
    return;
  }

  if ($c2->save('gpiod.properties') === FALSE) {
    $app->render('settings-garagedoors.php', array(
      'site'   => 'Garage',
      'title'  => 'Settings',
      'tab'    => 'Garage Doors',
      'status' => 'Failed to save GPIO settings',
    ));
    return;
  }

  $app->render('settings-garagedoors.php', array(
    'site'   => 'Garage',
    'title'  => 'Settings',
    'tab'    => 'Garage Doors',
    'status' => 'Settings saved successfully',
  ));
});
?>
