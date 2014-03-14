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
            <th>Name</th>
            <th>Sensor GPIO</th>
            <th>Relay GPIO</th>
            <th class="text-center">
              <button type="button" class="btn btn-default" id="add_door">Add Door</button>
            </th>
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
              <input type="text" name="name[]" class="form-control"
                     value="<?php echo $gd->getName(); ?>"
                     required="required" />
            </td>
            <td>
              <input type="text" name="sensorGpio[]" class="form-control"
                     value="<?php echo $gd->getSensorGpio(); ?>"
                     required="required" pattern="\d+" />
            </td>
            <td>
              <input type="text" name="relayGpio[]" class="form-control"
                     value="<?php echo $gd->getRelayGpio(); ?>"
                     required="required" pattern="\d+" />
            </td>
            <td class="text-center">
              <input type="button" class="btn btn-danger" name="remove" value="Remove" />
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
            'class'    : 'form-control',
            'required' : 'required',
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type'     : 'text',
            'name'     : 'sensorGpio[]',
            'class'    : 'form-control',
            'required' : 'required',
            'pattern'  : '\\d+',
          })
        ),
        $('<td>').append(
          $('<input>').attr({
            'type'     : 'text',
            'name'     : 'relayGpio[]',
            'class'    : 'form-control',
            'required' : 'required',
            'pattern'  : '\\d+',
          })
        ),
        $('<td>').attr({
          'class' : 'text-center'
        }).append(
          $('<input>').attr({
            'type'  : 'button',
            'class' : 'btn btn-danger',
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
