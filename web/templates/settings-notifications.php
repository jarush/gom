<?php
include 'header.inc.php';
include 'settings-tabs.inc.php'
?>

<legend>Twitter</legend>
<legend>Boxcar</legend>
<form class="form-horizontal">
  <div class="control-group">
    <label class="control-label" for="boxcar_email">Boxcar Email</label>
    <div class="controls">
      <input id="boxcar_email" type="email" placeholder="Email Address" required></input>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <button id="boxcar_subscribe" type="button" class="btn btn-primary">Subscribe</button>
      <button id="boxcar_test" type="button" class="btn">Test</button>
    </div>
  </div>
</form>

<script type="text/javascript">
  $('#boxcar_test').click(function() {
    $.ajax({
      type:     'POST',
      url:      '/settings/notifications/boxcar_test',
      dataType: 'json',
      data: {
        'action': 'test',
        'email':  $('#boxcar_email').val()
      },
    })
    .success(function(data, textStatus, xhr) {
      // TODO
    })
    .fail(function(xhr, textStatus, errorThrown) {
      // TODO
    });
  });
</script>

<?php include 'footer.inc.php'; ?>
