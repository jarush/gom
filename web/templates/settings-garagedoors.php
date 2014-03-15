<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';
include 'funcs.inc.php';

$garageDoors = GarageDoor::LoadGarageDoors(null);
?>

<div class="tab-body">
  <div id="alert-area">
  <?php
    if (isset($status)) {
      showAlert($status, $message);
    }
  ?>
  </div>

  <form id="form" method="post">
    <div class="panel panel-default">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Enabled</th>
            <th>Name</th>
            <th>Sensor GPIO</th>
            <th>Relay GPIO</th>
          </tr>
        </thead>
        <tbody>
<?php
foreach ($garageDoors as $garageDoor) {
  $enabled = $garageDoor->getEnabled();
  $number = $garageDoor->getNumber();
  $name = $garageDoor->getName();
  $sensorGpio = $garageDoor->getSensorGpio();
  $relayGpio = $garageDoor->getRelayGpio();
?>
          <tr door="<?php echo $number; ?>">
            <td class="text-center">
              <input type="checkbox" class="btn btn-danger" name="enabled[]"
                     door="<?php echo $number; ?>"
                     <?php echo $enabled ? 'checked="checked"' : ''; ?> />
            </td>
            <td>
              <input type="text" class="form-control" name="name[]"
                     value="<?php echo $name; ?>"
                     <?php echo (!$enabled) ? 'disabled="disabled"' : ''; ?>
                     required="required" />
            </td>
            <td>
              <input type="text" class="form-control" name="sensorGpio[]"
                     value="<?php echo $sensorGpio; ?>"
                     <?php echo (!$enabled) ? 'disabled="disabled"' : ''; ?>
                     required="required" pattern="\d+" />
            </td>
            <td>
              <input type="text" class="form-control" name="relayGpio[]"
                     value="<?php echo $relayGpio; ?>"
                     <?php echo (!$enabled) ? 'disabled="disabled"' : ''; ?>
                     required="required" pattern="\d+" />
            </td>
          </tr>
<?php
}
?>
        </tbody>
      </table>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="reset" class="btn">Reset</button>
    </div>
  </form>
</div>

<script type="text/javascript">
  $("input[name='enabled[]']").click(function() {
    // Get the door index
    var door = $(this).attr('door');

    if ($(this).prop('checked')) {
      $("tr[door='"+door+"'] input[type='text']").removeAttr('disabled');
    } else {
      $("tr[door='"+door+"'] input[type='text']").attr('disabled', 'disabled');
    }
  });
</script>

<?php include 'footer.inc.php'; ?>
