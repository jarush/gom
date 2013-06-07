<?php

$app->get('/', $authMw, function () use($app) {
  $app->render('status.php', array(
    'site'    => 'Garage',
    'title'   => 'Status',
  ));
});

?>
