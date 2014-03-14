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

  $eth = $req->post('eth');
  if (isset($eth)) {
    $config->set('eth', $eth);
  }

  $eth_dhcp = $req->post('eth_dhcp');
  if (isset($eth_dhcp)) {
    $config->set('eth_dhcp', $eth_dhcp);
  }

  $eth_static_address = $req->post('eth_static_address');
  if (isset($eth_static_address)) {
    $config->set('eth_static_address', $eth_static_address);
  }

  $eth_static_netmask = $req->post('eth_static_netmask');
  if (isset($eth_static_netmask)) {
    $config->set('eth_static_netmask', $eth_static_netmask);
  }

  $eth_static_gateway = $req->post('eth_static_gateway');
  if (isset($eth_static_gateway)) {
    $config->set('eth_static_gateway', $eth_static_gateway);
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
