<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';
include 'funcs.inc.php';

// Get the notifications configuration
$c = new Config('/config/login.properties');
$username = $c->get('username', '');
$password = $c->get('password', '');

?>

<div class="tab-body">
  <?php
    if (isset($status)) {
      showAlert($status, $message);
    }
  ?>

  <form class="form-horizontal" method="post">
    <legend>Login</legend>

    <div class="form-group">
      <label class="col-sm-3 control-label" for="username">Username</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" name="username"
               placeholder="Username" required="required" autocomplete="off"
               value="<?php echo $username; ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label" for="password1">Password</label>
      <div class="col-sm-9">
        <input type="password" class="form-control" name="password1"
               placeholder="Password" required="required"
               autocomplete="off"
               value="<?php echo $password; ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label" for="password2">Repeat Password</label>
      <div class="col-sm-9">
        <input type="password" class="form-control" name="password2"
               placeholder="Repeat Password" required="required"
               autocomplete="off"
               value="<?php echo $password; ?>" />
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="reset" class="btn">Reset</button>
    </div>
  </form>
</div>

<?php include 'footer.inc.php'; ?>
