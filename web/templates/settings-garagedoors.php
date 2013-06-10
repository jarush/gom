<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';

// Get the network configuration
//$c = new Config('garagedoors.properties');
$garageDoors = GarageDoor::loadGarageDoors('garagedoors.properties', null);
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

$numGarageDoors = 0;
foreach ($garageDoors as $gd) {
  $numGarageDoors++;

?>
      <tr>
        <td>
          <input type="text" name="name[]" value="<?php echo $gd->getName(); ?>"
                 required="required" />
        </td>
        <td>
          <input type="text" name="sensorGpio[]" value="<?php echo $gd->getSensorGpio(); ?>"
                 required="required" pattern="\d+" />
        </td>
        <td>
          <input type="text" name="relayGpio[]" value="<?php echo $gd->getRelayGpio(); ?>"
                 required="required" pattern="\d+" />
        </td>
        <td>
          <input type="button" class="btn" name="remove" value="Remove" />
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
  numGarageDoors = <?php echo $numGarageDoors; ?>;

  $('#add_door').click(function() {
    if (numGarageDoors >= 10) {
      return;
    }

    numGarageDoors++;

    $('tbody').append(
      $('<tr>').append([
        $('<td>').append(
          $('<input>').attr({
            'type'     : 'text',
            'name'     : 'name[]',
            'required' : 'required',
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type'     : 'text',
            'name'     : 'sensorGpio[]',
            'required' : 'required',
            'pattern'  : '\\d+',
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type'     : 'text',
            'name'     : 'relayGpio[]',
            'required' : 'required',
            'pattern'  : '\\d+',
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type'  : 'button',
            'class' : 'btn',
            'name'  : 'remove',
            'value' : 'Remove'
          }).click(function() {
            $(this).parents('tr').remove();
            numGarageDoors--;
          })
        ),
      ])
    );
  });

  $('input[name="remove"]').click(function() {
    $(this).parents('tr').remove();
    numGarageDoors--;
  });
</script>

<?php include 'footer.inc.php'; ?>
