<?php

$app->get('/settings/network', $authMw, function () use($app) {
  $app->render('settings-network.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Network',
  ));
});

$app->post('/settings/network', $authMw, function () use($app) {
  $config = new Config('network.properties');
  $req = $app->request();

  $ethEnabled = $req->post('ethEnabled');
  if (isset($ethEnabled)) {
    $config->set('eth.enabled', $ethEnabled);
  }

  $ethDhcp = $req->post('ethDhcp');
  if (isset($ethDhcp)) {
    $config->set('eth.dhcp', $ethDhcp);
  }

  $ethStaticAddress = $req->post('ethStaticAddress');
  if (isset($ethStaticAddress)) {
    $config->set('eth.static.address', $ethStaticAddress);
  }

  $ethStaticNetmask = $req->post('ethStaticNetmask');
  if (isset($ethStaticNetmask)) {
    $config->set('eth.static.netmask', $ethStaticNetmask);
  }

  $ethStaticGateway = $req->post('ethStaticGateway');
  if (isset($ethStaticGateway)) {
    $config->set('eth.static.gateway', $ethStaticGateway);
  }

  if ($config->save('network.properties') === FALSE) {
    $app->render('settings-network.php', array(
      'site'    => 'Garage',
      'title'   => 'Settings',
      'tab'     => 'Network',
      'status'  => 'error',
      'message' => 'Failed to save settings',
    ));
    return;
  }

  $app->render('settings-network.php', array(
    'site'    => 'Garage',
    'title'   => 'Settings',
    'tab'     => 'Network',
    'status'  => 'success',
    'message' => 'Settings saved successfully',
  ));
});

?>
