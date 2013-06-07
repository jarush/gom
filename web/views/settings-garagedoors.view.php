<?php

$app->get('/settings/garagedoors', $authMw, function () use($app) {
  $app->render('settings-garagedoors.php', array(
    'site'  => 'Garage',
    'title' => 'Settings',
    'tab'   => 'Garage Doors',
  ));
});

?>
