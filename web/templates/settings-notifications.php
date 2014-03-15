<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';
include 'funcs.inc.php';

// Get the notifications configuration
$c = new Config('gomd.properties');
?>

<div class="tab-body">
  <div id="alert-area">
  <?php
    if (isset($status)) {
      showAlert($status, $message);
    }
  ?>
  </div>

  <form class="form-horizontal" method="post">
    <legend>Boxcar</legend>

    <?php
      $boxcarEnabled = $c->get('boxcar.enabled', 'false');
      $boxcarAccessToken = $c->get('boxcar.access_token', '');
      $boxcarSound = $c->get('boxcar.sound', 'beep-crisp');
    ?>

    <div class="form-group">
      <label class="col-sm-2 control-label" for="boxcarEnabled">Enabled</label>
      <div class="col-sm-10">
        <label class="radio-inline">
          <input type="radio" name="boxcarEnabled" value="true"
                 <?php echo ($boxcarEnabled == 'true') ? 'checked' : ''?>/>Enabled
        </label>
        <label class="radio-inline">
          <input type="radio" name="boxcarEnabled" value="false"
                 <?php echo ($boxcarEnabled != 'true') ? 'checked' : ''?>/>Disabled
        </label>
      </div>
    </div>

    <fieldset id="boxcarFields"
              <?php echo ($boxcarEnabled != 'true') ? 'disabled="disabled"' : ''; ?>>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="boxcarAccessToken">Access Token</label>
        <div class="col-sm-10">
          <input type="text" class="form-control"
                 name="boxcarAccessToken" id="boxcarAccessToken"
                 placeholder="Access Token" required="required"
                 value="<?php echo $boxcarAccessToken; ?>" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="boxcarSound">Sound</label>
        <div class="col-sm-10">
          <select class="form-control" name="boxcarSound" id="boxcarSound">
            <?php
              $sounds = array(
                'beep-crisp',
                'beep-soft',
                'bell-modern',
                'bell-one-tone',
                'bell-simple',
                'bell-triple',
                'bird-1',
                'bird-2',
                'boing',
                'cash',
                'clanging',
                'detonator-charge',
                'digital-alarm',
                'done',
                'echo',
                'flourish',
                'harp',
                'light',
                'magic-chime',
                'magic-coin',
                'notifier-1',
                'notifier-2',
                'notifier-3',
                'orchestral-long',
                'orchestral-short',
                'score',
                'success',
                'up'
              );
              foreach ($sounds as $sound) {
                $selected = $sound == $boxcarSound ? 'selected' : '';
                echo "<option $selected>$sound</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="button" id="boxcarTest" class="btn btn-primary">Test</button>
        </div>
      </div>
    </fieldset>

    <legend>Email</legend>

    <?php
      $emailEnabled = $c->get('email.enabled', 'false');
      $emailUrl = $c->get('email.url', '');
      $emailUsername = $c->get('email.username', '');
      $emailPassword = $c->get('email.password', '');
      $emailFrom = $c->get('email.from', '');
      $emailTo = $c->get('email.to', '');
    ?>

    <div class="form-group">
      <label class="col-sm-2 control-label" for="emailEnabled">Enabled</label>
      <div class="col-sm-10">
        <label class="radio-inline">
          <input type="radio" name="emailEnabled" value="true"
                 <?php echo ($emailEnabled == 'true') ? 'checked' : ''?>/>Enabled
        </label>
        <label class="radio-inline">
          <input type="radio" name="emailEnabled" value="false"
                 <?php echo ($emailEnabled != 'true') ? 'checked' : ''?>/>Disabled
        </label>
      </div>
    </div>

    <fieldset id="emailFields"
              <?php echo ($emailEnabled != 'true') ? 'disabled="disabled"' : ''; ?>>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="emailUrl">URL</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="emailUrl"
                 placeholder="URL" required="required"
                 value="<?php echo $emailUrl; ?>" />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="emailUsername">Username</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="emailUsername"
                 placeholder="Username" required="required"
                 value="<?php echo $emailUsername; ?>" />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="emailPassword">Password</label>
        <div class="col-sm-10">
          <input type="password" class="form-control" name="emailPassword"
                 placeholder="Password" required="required"
                 value="<?php echo $emailPassword; ?>" />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="emailFrom">From</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="emailFrom"
                 placeholder="From" required="required"
                 value="<?php echo $emailFrom; ?>" />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="emailTo">To</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="emailTo"
                 placeholder="To" required="required"
                 value="<?php echo $emailTo; ?>" />
        </div>
      </div>
    </fieldset>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="reset" class="btn">Reset</button>
    </div>
  </form>
</div>

<script type="text/javascript">
  $("input[name='boxcarEnabled']").click(function() {
    var disabled = $(this).val() != 'true';
    $("#boxcarFields").prop('disabled', disabled);
  });

  $("input[name='emailEnabled']").click(function() {
    var disabled = $(this).val() != 'true';
    $("#emailFields").prop('disabled', disabled);
  });

  $('#boxcarTest').click(function() {
    $.ajax({
      type:     'POST',
      url:      '/settings/notifications/boxcar_test',
      dataType: 'json',
      data: {
        'boxcarAccessToken': $('#boxcarAccessToken').val(),
        'boxcarSound':       $('#boxcarSound').val()
      },
    })
    .done(function(data, textStatus, xhr) {
      showAlert('alert-success', 'Success', 'Test message sent to Boxcar');
    })
    .fail(function(xhr, textStatus, errorThrown) {
      response = xhr.responseJSON;
      showAlert('alert-danger', 'Error', response.message);
    });
  });
</script>

<?php include 'footer.inc.php'; ?>
