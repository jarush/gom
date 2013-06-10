<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';

// Get the network configuration
$c = new Config('garagedoors.properties');
?>

<?php
if (isset($status)) {
  echo '<div class="alert alert-success">';
  echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
  echo $status;
  echo '</div>';
}
?>

<form id="form" method="post">
  <p>
    <input type="button" class="btn" id="add_door" value="Add Door" />
  </p>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>Sensor GPIO</th>
        <th>Relay GPIO</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
<?php

for ($i = 0; $i < 10; $i++) {
  $name = $c->get('name' . $i, null);
  $sensorGpio = $c->get('sensorGpio' . $i, null);
  $relayGpio = $c->get('relayGpio' . $i, null);

  if ($name == null || $sensorGpio == null || $relayGpio == null) {
    continue;
  }

?>
      <tr>
        <td>
          <input type="text" name="name[]" value="<?php echo $name; ?>"
                 required="required" />
        </td>
        <td>
          <input type="text" name="sensorGpio[]" value="<?php echo $sensorGpio; ?>"
                 required="required" pattern="\d+" />
        </td>
        <td>
          <input type="text" name="relayGpio[]" value="<?php echo $relayGpio; ?>"
                 required="required" pattern="\d+" />
        </td>
        <td>
          <input type='button' id='remove' value='Remove' />
        </td>
      </tr>
<?php
}
?>
    </tbody>
  </table>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="reset" class="btn">Reset</button>
  </div>
</form>

<script type="text/javascript">
  $('#add_door').click(function() {
    numDoors = 0;
    $('tbody').append(
      $('<tr>').append([
        $('<td>').append(
          $('<input>').attr({
            'type' : 'text',
            'name' : 'name[]'
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type' : 'text',
            'name' : 'sensorGpio[]'
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type' : 'text',
            'name' : 'relayGpio[]'
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type'  : 'button',
            'id'    : 'remove_door',
            'class' : 'btn',
            'value' : 'Remove'
          }).click(function() {
            $(this).parents('tr').remove();
          })
        ),
      ])
    );
  });

  $('#remove_door').click(function() {
    $(this).parents('tr').remove();
  });
</script>

<?php include 'footer.inc.php'; ?>
