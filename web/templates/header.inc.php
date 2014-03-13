<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $site; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">

    <!-- javascript libraries -->
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/common.js"></script>

    <!-- iOS specific settings -->
    <?php include 'ios.inc.php'; ?>
  </head>

  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <span class="navbar-brand"><?php echo $site; ?></span>
        </div>

        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li <?php if ($title === "Status") echo 'class="active"'; ?>>
              <a href="/">Status</a>
            </li>
            <li <?php if ($title === "Settings") echo 'class="active"'; ?>>
              <a href="/settings/garagedoors">Settings</a>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
      <h2><?php echo $title; ?></h2>
