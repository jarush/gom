<?php include 'header.inc.php'; ?>

<div id="alert-area">
</div>

<?php
$n = count($garageDoors);
for ($i = 0; $i < $n; $i++) {
  $name = $garageDoors[$i]->getName();
  $status = $garageDoors[$i]->getStatus();
  $buttonClass = '';
  if ($status == 'Open') {
    $buttonClass = 'btn-danger';
  }
?>

<div class="well door" door="<?php echo $i; ?>">
  <h3>Door: <?php echo $name; ?></h3>

  <p class="lead" door="<?php echo $i; ?>">
    Status: <?php echo $status; ?>
  </p>

  <button class="btn btn-large btn-primary <?php echo $buttonClass; ?>"
          door="<?php echo $i; ?>"
          status="<?php echo $status; ?>">
    <?php echo $status == "Open" ? "Close" : "Open"; ?>
  </button>
</div>

<?php
}
?>

<script type="text/javascript">
  function showAlert(alertClass, title, message) {
    var html = '';

    html += '<div id="alert" class="alert ' + alertClass + '">';
    html += '  <button type="button" class="close" data-dismiss="alert">&times;</button>';
    if (title) {
      html += '  <h4>' + title + '</h4>';
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
      showAlert('alert-error', 'Error', 'Failed to send command to garage door opener');
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

        var p = div.children('p');
        p.text('Status: ' + data.doorStatus);

        var btn = div.children('button');
        btn.attr('status', data.doorStatus);
        if (data.doorStatus == 'Closed') {
          btn.text('Open');
          btn.removeClass('btn-danger');
        } else {
          btn.text('Close');
          btn.addClass('btn-danger');
        }
        btn.removeClass('disabled');
      })
    });
  }
</script>

<?php include 'footer.inc.php'; ?>
