<?php
include 'header.inc.php';
include 'settings-tabs.inc.php'
?>

<div class="tab-body">
  <legend>Firmware Upgrade</legend>
  <form id="form" class="form-horizontal" enctype="multipart/form-data" method="post">
    <div class="form-group">
      <label class="col-sm-2 control-label" for="pretty-file">Firmware File</label>
      <input id="file" type="file" name="file" class="hide"/>
      <div class="col-sm-10">
        <div class="input-group">
          <input type="text" class="form-control" id="pretty-file"
                 onclick="$('#file').click();">
          <div class="input-group-btn">
            <button type="button" class="btn btn-default"
                    onclick="$('#file').click(); return false;">Browse</button>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <input type="button" class="btn btn-primary" id="upgrade" value="Upgrade" disabled="disabled"/>
      </div>
    </div>
  </form>

  <div id="upgradeModal" class="modal hide fade">
    <div class="modal-header">
      <h3>Firmware Upgrade</h3>
    </div>

    <div class="modal-body">
      <h4 id="upgradeStatus">Uploading Firmware...</h4>
      <div id="progress" class="progress progress-striped active">
        <div id="bar" class="bar" style="width: 0%;"></div>
      </div>
    </div>

    <div class="modal-footer">
      <a id="upgradeClose" href="#" class="btn disabled">Close</a>
      <a id="upgradeReboot" href="#" class="btn btn-primary disabled">Reboot</a>
    </div>
  </div>

  <legend>Factory Defaults</legend>
</div>

<script>
$('#file').change(function() {
  var filename = $(this).val();
  filename = filename.replace("C:\\fakepath\\", "");

  $('#pretty-file').val(filename);

  $('#upgrade').prop('disabled', filename.length == 0);
});

$('#upgradeClose').click(function() {
  $('#upgradeModal').modal('hide')
});

$('#upgradeRestart').click(function() {
  $('#upgradeModal').modal('hide')
});

$('#upgrade').click(function() {
  // Reset the modal
  $('#upgradeClose').addClass('disabled');
  $('#upgradeReboot').addClass('disabled');
  $('#progress').removeClass('progress-success');
  $('#progress').removeClass('progress-danger');
  $('#bar').css('width', '0%');
  $('#upgradeStatus').text('Uploading Firmware...');

  // Display the modal
  $('#upgradeModal').modal({
    keyboard: false,
    backdrop: 'static'
  });

  // Set the file in the FormData
  var formData = new FormData($('#form')[0]);

  $.ajax({
    type: 'POST',
    url: "/settings/firmwareupgrade",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    xhr: function() {
      xhr = $.ajaxSettings.xhr();
      if (xhr.upload) {
        xhr.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total * 100.0;
            $('#bar').css('width', percentComplete + '%');
            if (percentComplete >= 100) {
              // Update the status text
              $('#upgradeStatus').text('Processing...');
            }
          }
        }, false);
      }
      return xhr;
    },
    success: function(data, textStatus, xhr) {
      // Make sure the progress is 100%
      $('#bar').css('width', '100%');

      // Update the status text
      $('#upgradeStatus').text('Firmware upgraded, please reboot');

      // Change the progress bar color
      $('#progress').addClass('progress-success');

      // Enable the close and reboot buttons
      $('#upgradeClose').removeClass('disabled');
      $('#upgradeReboot').removeClass('disabled');
    },
    error: function(xhr, textStatus, errorThrown) {
      // Make sure the progress is 100%
      $('#bar').css('width', '100%');

      // Update the status text
      $('#upgradeStatus').text('Failed to upgrade firmware');

      // Change the progress bar color
      $('#progress').addClass('progress-danger');

      // Enable the close and reboot buttons
      $('#upgradeClose').removeClass('disabled');
      $('#upgradeReboot').removeClass('disabled');
    }
  });
});
</script>

<?php include 'footer.inc.php'; ?>
