<?php

$app->get('/settings/garagedoors', $authMw, function () use($app) {
  $app->render('settings-garagedoors.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Garage Doors',
  ));
});

$app->post('/settings/garagedoors', $authMw, function () use($app) {
  $config = new Config();
  $req = $app->request();

  $name = $req->post('name');
  $sensorGpio = $req->post('sensorGpio');
  $relayGpio = $req->post('relayGpio');

  $n = count($name);

  $door = 0;
  for ($i = 0; $i < $n; $i++) {
    // Skip the door is no pin is set
    if (!isset($name[$i]) || !isset($sensorGpio[$i]) || !isset($relayGpio[$i])) {
      continue;
    }

    $config->set('name' . $door, $name[$i]);
    $config->set('sensorGpio' . $door, $sensorGpio[$i]);
    $config->set('relayGpio' . $door, $relayGpio[$i]);

    $door++;
  }

  if ($config->save('garagedoors.properties') === FALSE) {
    $app->render('settings-garagedoors.php', array(
      'site'   => 'Garage',
      'title'  => 'Settings',
      'tab'    => 'Network',
      'status' => 'Failed to save settings',
    ));
    return;
  }

  $app->render('settings-garagedoors.php', array(
    'site'   => 'Garage',
    'title'  => 'Settings',
    'tab'    => 'Network',
    'status' => 'Settings saved successfully',
  ));
});
?>
