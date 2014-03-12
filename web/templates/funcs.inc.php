<?php

function showAlert($status, $message) {
  $alertClass = '';
  if ($status === 'success') {
    $alertClass = 'alert-success';
  } else if ($status === 'warning') {
    $alertClass = 'alert-warning';
  } else if ($status === 'error') {
    $alertClass = 'alert-danger';
  }

  echo "<div id='alert' class='alert $alertClass alert-dismissable'>";
  echo "  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
  echo "  $message";
  echo "</div>";
}

?>
