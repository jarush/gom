<?php
include 'header.inc.php';
include 'funcs.inc.php';
?>

<?php
  if (isset($status)) {
    showAlert($status, $message);
  }
?>

<?php include 'footer.inc.php'; ?>
