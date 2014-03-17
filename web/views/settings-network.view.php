<?php

$app->get('/settings/network', $authMw, function () use($app) {
  $app->render('settings-network.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Network',
  ));
});

$app->post('/settings/network', $authMw, function () use($app) {
  $req = $app->request();

  $interfaces = NetworkInterface::LoadNetworkInterfaces('/etc/network/interfaces');
  $interface = $interfaces['eth0'];
  if (!isset($interface)) {
    $interface = new NetworkInterface();
    $interface->setName('eth0');
    $interface->setAddressFamily('inet');
    $interface->setMethod('dhcp');

    $interfaces['eth0'] = $interface;
  }

  $ethDhcp = $req->post('ethDhcp');
  if (isset($ethDhcp) && $ethDhcp == 'enabled') {
    $interface->setMethod('dhcp');
  } else {
    $interface->setMethod('static');
  }

  $ethStaticAddress = $req->post('ethStaticAddress');
  if (isset($ethStaticAddress)) {
    $interface->setOption('address', $ethStaticAddress);
  }

  $ethStaticNetmask = $req->post('ethStaticNetmask');
  if (isset($ethStaticNetmask)) {
    $interface->setOption('netmask', $ethStaticNetmask);
  }

  $ethStaticGateway = $req->post('ethStaticGateway');
  if (isset($ethStaticGateway)) {
    $interface->setOption('gateway', $ethStaticGateway);
  }

  if (NetworkInterface::SaveNetworkInterfaces('/etc/network/interfaces',
      $interfaces) === FALSE) {
    $app->render('settings-network.php', array(
      'site'    => 'Garage',
      'title'   => 'Settings',
      'tab'     => 'Network',
      'status'  => 'error',
      'message' => 'Failed to save settings',
    ));
    return;
  }

  # Initiate a reboot in a couple of seconds
  exec('(sleep 2; sudo /bin/busybox reboot) &> /dev/null &');

  $app->render('reboot.php', array(
    'site'    => 'Garage',
    'title'   => 'Settings',
    'status'  => 'success',
    'message' => 'Settings saved successfully, rebooting system',
  ));
});

?>
