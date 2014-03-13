<?php
include 'header.inc.php';
include 'funcs.inc.php';
?>

<div id="alert-area">
<?php
if (isset($status)) {
  showAlert($status, $message);
}
?>
</div>

<?php
$n = count($garageDoors);
for ($i = 0; $i < $n; $i++) {
  $name = $garageDoors[$i]->getName();
  $status = $garageDoors[$i]->getStatus();
  $panelClass = 'panel-default';
  $statusClass = 'text-success';
  $buttonClass = '';
  if ($status == 'Open') {
    $panelClass = 'panel-danger';
    $statusClass = 'text-danger';
    $buttonClass = 'btn-danger';
  }
?>

<div class="panel <?php echo $panelClass; ?>"
     door="<?php echo $i; ?>">
  <div class="panel-heading">
    <h3 class="panel-title">
      Door: <?php echo $name; ?>
      <strong id="status" class="pull-right <?php echo $statusClass; ?>"><?php echo $status; ?></strong>
    </h3>
  </div>

  <div class="panel-body">
    <button class="btn btn-large btn-primary btn-block <?php echo $buttonClass; ?>"
            door="<?php echo $i; ?>"
            status="<?php echo $status; ?>">
      <?php echo $status == "Open" ? "Close" : "Open"; ?>
    </button>
  </div>
</div>

<?php
}
?>

<script type="text/javascript">
  function showAlert(alertClass, title, message) {
    var html = '';

    html += '<div id="alert" class="alert ' + alertClass + ' alert-dismissable">';
    html += '  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    if (title) {
      html += '<strong>' + title + ':</strong> ';
    }
    html += message;
    html += '</div>';

    $('#alert-area').html(html);
  }

  $('button[door]').click(function() {
    var btn = $(this);

    // Ignore this click if the button is disabled
    if (btn.hasClass('disabled')) {
      return;
    }

    // Get the door index
    var door = btn.attr('door');

    // Disable the button and update the button text
    if (btn.attr('status') == 'Open') {
      btn.addClass('disabled');
      btn.text('Closing...');
    } else {
      btn.addClass('disabled');
      btn.text('Opening...');
    }

    // Send the command
    $.ajax({
      type:     'POST',
      url:      '/door/pressbutton',
      dataType: 'json',
      data: {
        'door': door,
      },
    })
    .success(function(data, textStatus, xhr) {
      showAlert('alert-success', 'Success', 'Command sent to garage door opener');
    })
    .fail(function(xhr, textStatus, errorThrown) {
      showAlert('alert-danger', 'Error', 'Failed to send command to garage door opener');
    })
    .complete(function() {
      window.setTimeout(updateStatus, 5000);
    });
  });

  function updateStatus() {
    $('div[door]').each(function(index) {
      var div = $(this);

      // Get the door index
      var door = $(this).attr('door');

      // Send the command
      $.ajax({
        type:     'POST',
        url:      '/door/status',
        dataType: 'json',
        data: {
          'door': door,
        },
      })
      .success(function(data, textStatus, xhr) {
        console.log(data);

        var status = div.find('#status');
        status.text(data.doorStatus);

        var btn = div.find('button');
        btn.attr('status', data.doorStatus);

        if (data.doorStatus == 'Closed') {
          div.removeClass('panel-danger');
          div.addClass('panel-default');

          status.removeClass('text-danger');
          status.addClass('text-success');

          btn.text('Open');
          btn.removeClass('btn-danger');
        } else {
          div.removeClass('panel-default');
          div.addClass('panel-danger');

          status.removeClass('text-success');
          status.addClass('text-danger');

          btn.text('Close');
          btn.addClass('btn-danger');
        }

        btn.removeClass('disabled');
      })
    });
  }
</script>

<?php include 'footer.inc.php'; ?>
