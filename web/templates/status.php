<?php include 'header.inc.php'; ?>

<div class="well door">
  <h3>Garage Door 1</h3>

  <p class="lead">Status: <?php echo $garageDoor->getStatus(); ?></p>
  <button class="btn btn-large btn-primary" door="1" status="closed">Open</button>
</div>

<script type="text/javascript">
  $('button[door]').click(function() {
    var btn = $(this);
    if (btn.hasClass('disabled')) {
      return;
    }

    if (btn.attr('status') == 'open') {
      btn.addClass('disabled');
      btn.text('Closing...');
    } else {
      btn.addClass('disabled');
      btn.text('Opening...');
    }

    $.ajax({
      type:     'POST',
      url:      '/door/pressbutton',
      dataType: 'json',
      data: {
        'door': '1',
      },
    })
    .success(function(data, textStatus, xhr) {
      console.log('Pressed door button');
      // TODO
    })
    .fail(function(xhr, textStatus, errorThrown) {
      console.log('Error pressing door button');
      // TODO
    })
    .complete(function() {
      if (btn.attr('status') == 'open') {
        btn.attr('status', 'closed');
        btn.text('Open');
        btn.removeClass('btn-danger');
      } else {
        btn.attr('status', 'open');
        btn.text('Close');
        btn.addClass('btn-danger');
      }
      btn.removeClass('disabled');
    });
  });

/*
  $("button[door][status='closed']").click(function() {
    var btn = $(this);
    if (btn.hasClass('disabled')) {
      return;
    }

    if (btn.attr('status') == 'open') {
      btn.addClass('disabled');
      btn.text('Closing...');
    } else {
      btn.addClass('disabled');
      btn.text('Opening...');
    }

    window.setTimeout(function() {
      if (btn.attr('status') == 'open') {
        btn.attr('status', 'closed');
        btn.text('Open');
        btn.removeClass('btn-danger');
      } else {
        btn.attr('status', 'open');
        btn.text('Close');
        btn.addClass('btn-danger');
      }
      btn.removeClass('disabled');
    }, 4000);
  });
*/
</script>

<?php include 'footer.inc.php'; ?>
