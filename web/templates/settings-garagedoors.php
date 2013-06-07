<?php
include 'header.inc.php';
include 'settings-tabs.inc.php'
?>

<form>
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>Door</th>
        <th>Enabled</th>
        <th>Sensor GPIO</th>
        <th>Relay GPIO</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Enabled</td>
        <td>GPIO</td>
        <td>GPIO</td>
      </tr>
      <tr>
        <td>2</td>
        <td>Enabled</td>
        <td>GPIO</td>
        <td>GPIO</td>
      </tr>
    </tbody>
  </table>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="reset" class="btn">Reset</button>
  </div>
</form>

<?php include 'footer.inc.php'; ?>
