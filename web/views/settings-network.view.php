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

  $wlan = $req->post('wlan');
  if (isset($wlan)) {
    $config->set('wlan', $wlan);
  }

  $wlan_ssid = $req->post('wlan_ssid');
  if (isset($wlan_ssid)) {
    $config->set('wlan_ssid', $wlan_ssid);
  }

  $wlan_security_mode = $req->post('wlan_security_mode');
  if (isset($wlan_security_mode)) {
    $config->set('wlan_security_mode', $wlan_security_mode);
  }

  $wlan_key = $req->post('wlan_key');
  if (isset($wlan_key)) {
    $config->set('wlan_key', $wlan_key);
  }

  $wlan_dhcp = $req->post('wlan_dhcp');
  if (isset($wlan_dhcp)) {
    $config->set('wlan_dhcp', $wlan_dhcp);
  }

  $wlan_static_address = $req->post('wlan_static_address');
  if (isset($wlan_static_address)) {
    $config->set('wlan_static_address', $wlan_static_address);
  }

  $wlan_static_netmask = $req->post('wlan_static_netmask');
  if (isset($wlan_static_netmask)) {
    $config->set('wlan_static_netmask', $wlan_static_netmask);
  }

  $wlan_static_gateway = $req->post('wlan_static_gateway');
  if (isset($wlan_static_gateway)) {
    $config->set('wlan_static_gateway', $wlan_static_gateway);
  }

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
      'site'   => 'Garage',
      'title'  => 'Settings',
      'tab'    => 'Network',
      'status' => 'Failed to save settings',
    ));
    return;
  }

  $app->render('settings-network.php', array(
    'site'   => 'Garage',
    'title'  => 'Settings',
    'tab'    => 'Network',
    'status' => 'Settings saved successfully',
  ));
});

?>
