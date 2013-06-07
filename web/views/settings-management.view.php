<?php

$app->get('/settings/management', $authMw, function () use($app) {
  $app->render('settings-management.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Management',
  ));
});

?>
