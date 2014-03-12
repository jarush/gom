<?php
$tabs = array(
  'Garage Doors' => '/settings/garagedoors',
  'Network' => '/settings/network',
  'Notifications' => '/settings/notifications',
  'Management' => '/settings/management',
  'Firmware Upgrade' => '/settings/firmwareupgrade',
);
?>

<ul class="nav nav-tabs hidden-xs">
<?php
foreach ($tabs as $name => $page) {
  $style = $name == $tab ? 'class="active"' : '';
  echo "<li $style><a href='$page'>$name</a></li>\n";
}
?>

</ul>
<div class="well well-small visible-xs">
  <ul class="nav nav-pills nav-stacked">
<?php
foreach ($tabs as $name => $page) {
    $style = $name == $tab ? 'class="active"' : '';
    echo "<li $style><a href='$page'>$name</a></li>\n";
  }
?>
  </ul>
</div>

