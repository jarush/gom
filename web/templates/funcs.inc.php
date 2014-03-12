<?php

function showAlert($status, $message) {
  $alertClass = '';
  if ($status === 'success') {
    $alertClass = 'alert-success';
  } else if ($status === 'warning') {
    $alertClass = 'alert-warning';
  } else if ($status === 'error') {
    $alertClass = 'alert-error';
  }

  echo "<div id='alert' class='alert $alertClass'>";
  echo "  <button type='button' class='close' data-dismiss='alert'>&times;</button>";
  echo "<h4>$message</h4>";
  echo "</div>";
}

?>
