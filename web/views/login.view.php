<?php

$authMw = function() use($app) {
  $req = $app->request();

  // Check if running on an iPhone or iPad
  $userAgent = $req->getUserAgent();
  if (strstr($userAgent, 'iPhone') || strstr($userAgent, 'iPad')) {
    // Extend the session cookie lifetime
    $app->setCookie(session_name(), session_id(), time() + (3600*24*100));
  }

  // Check if the session is set
  if (!empty($_SESSION['username'])) {
    return;
  }

  // Check if there's a cookie to remember the login
  $username = $app->getCookie('username');
  $password = $app->getCookie('password');
  if (!empty($username) && !empty($password)) {
    // Check the username and password
    $config = new Config('login.properties');
    if ($username == $config->get('username', null) &&
        $password == $config->get('password', null)) {
      // Update the cookie with the current time
      $app->setCookie('username', $username, time() + (3600*24*100), '/', '', true);
      $app->setCookie('password', $password, time() + (3600*24*100), '/', '', true);

      // Set the session variable
      $_SESSION['username'] = $username;

      $app->redirect('/');

      return;
    } else {
      $app->deleteCookie('username');
      $app->deleteCookie('password');
    }
  }

  $app->redirect('/login');
};

$app->get('/login', function() use($app) {
  $app->render('login.php', array(
    'site' => 'Garage',
  ));
});

$app->post('/login', function() use($app) {
  $req = $app->request();

  // Get the username and password
  $username = $req->post('username');
  $password = $req->post('password');

  // Check the username and password
  $config = new Config('login.properties');
  if ($username != $config->get('username', null) ||
      $password != $config->get('password', null)) {
    $app->redirect('/login');
    return;
  }

  // Set the cookie if we're supposed to remember
  $remember = $req->post('remember');
  if ($remember == true) {
    $app->setCookie('username', $username, time() + (3600*24*100), '/', '', true);
    $app->setCookie('password', $password, time() + (3600*24*100), '/', '', true);
  }

  // Set the session variable
  $_SESSION['username'] = $username;

  $app->redirect('/');
});

$app->get('/logout', function() use($app) {
  // Clear out the session
  session_destroy();
  $_SESSION = array();

  // Delete the cookie
  $app->deleteCookie('username');
  $app->deleteCookie('password');

  // Redirect to the login page
  $app->redirect('/login');
});

?>
