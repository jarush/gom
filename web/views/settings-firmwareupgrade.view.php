<?php

$app->get('/settings/firmwareupgrade', $authMw, function () use($app) {
  $app->render('settings-firmwareupgrade.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Firmware Upgrade',
  ));
});

$app->post('/settings/firmwareupgrade', $authMw, function () use($app) {
  error_log('upgrade');
});

?>
