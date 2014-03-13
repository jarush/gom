<?php
include 'header.inc.php';
include 'settings-tabs.inc.php'
?>

<div class="tab-body">
  <form class="form-horizontal">
    <fieldset>
      <legend>Twitter</legend>
    </fieldset>

    <fieldset>
      <legend>Boxcar</legend>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="boxcar_email">Boxcar Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" name="boxcar_email" id="boxcar_email"
                 placeholder="Email Address" required="required"></input>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="button" id="boxcar_subscribe" class="btn btn-primary">Subscribe</button>
          <button type="button" id="boxcar_test" class="btn">Test</button>
        </div>
      </div>
    <fieldset>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="reset" class="btn">Reset</button>
    </div>
  </form>
</div>

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
