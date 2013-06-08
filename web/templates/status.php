<?php include 'header.inc.php'; ?>

<div id="alert-area">
</div>

<div class="well door" door="1">
  <h3>Garage Door 1</h3>

  <p class="lead" door="1">Status: <?php echo $garageDoor->getStatus(); ?></p>
  <button class="btn btn-large btn-primary" door="1" status="closed">Open</button>
</div>

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
    if (btn.attr('status') == 'open') {
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
        if (data.doorStatus == 'Closed') {
          btn.attr('status', 'closed');
          btn.text('Open');
          btn.removeClass('btn-danger');
        } else {
          btn.attr('status', 'open');
          btn.text('Close');
          btn.addClass('btn-danger');
        }
        btn.removeClass('disabled');
      })
    });
  }
</script>

<?php include 'footer.inc.php'; ?>
