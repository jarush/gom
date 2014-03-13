<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $site; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
      }
    </style>

    <!-- iOS specific settings -->
    <?php include 'ios.inc.php'; ?>
  </head>

  <body>
    <!-- Main Container -->
    <div class="container">
      <div class="col-sm-offset-3 col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading text-center">
            <h2>Login</h2>
          </div>
          <div class="panel-body">
            <form class="form" action="/login" method="post">
              <div class="form-group">
                <input name="username" type="text" class="form-control" placeholder="Username">
              </div>
              <div class="form-group">
                <input name="password" type="password" class="form-control" placeholder="Password">
              </div>
              <div class="checkbox">
                <label>
                  <input name="remember" type="checkbox"> Remember me
                </label>
              </div>
              <button class="btn btn-large btn-block btn-primary" type="submit">Sign in</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Javascript Libraries - Placed at the end of the document so the pages load faster -->
    <script src="/js/jquery-1.9.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  </body>
</html>

