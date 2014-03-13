<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';
include 'funcs.inc.php';

// Get the notifications configuration
$c = new Config('notifications.properties');
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
    <fieldset>
      <legend>Boxcar</legend>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="boxcar_access_token">Access Token</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="boxcar_access_token" id="boxcar_access_token"
                 placeholder="Access Token" required="required"
                 value="<?php echo $c->get('boxcar_access_token', ''); ?>" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="boxcar_sound">Sound</label>
        <div class="col-sm-10">
          <select class="form-control" name="boxcar_sound" id="boxcar_sound">
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
              $selected_sound = $c->get('boxcar_sound', null);
              foreach ($sounds as $sound) {
                $selected = $sound == $selected_sound ? 'selected' : '';
                echo "<option $selected>$sound</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="button" id="boxcar_test" class="btn btn-primary">Test</button>
        </div>
      </div>
    <fieldset>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="reset" class="btn">Reset</button>
    </div>
  </form>
</div>

<script type="text/javascript">
  $('#boxcar_test').click(function() {
    $.ajax({
      type:     'POST',
      url:      '/settings/notifications/boxcar_test',
      dataType: 'json',
      data: {
        'access_token': $('#boxcar_access_token').val(),
        'sound':        $('#boxcar_sound').val()
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
