<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';
include 'funcs.inc.php';

// Get the network configuration
$c = new Config('network.properties');

// Check if the eth interface and eth dhcp are enabled
$eth = $c->get('eth', null) == 'enabled';
$eth_dhcp = $c->get('eth_dhcp', null) == 'enabled';
if (!$eth) {
  $eth_disabled = 'disabled';
  $eth_dhcp_disabled = 'disabled';
} else if ($eth_dhcp) {
  $eth_disabled = '';
  $eth_dhcp_disabled = 'disabled';
} else {
  $eth_disabled = '';
  $eth_dhcp_disabled = '';
}
?>

<div class="tab-body">
  <?php
    if (isset($status)) {
      showAlert($status, $message);
    }
  ?>

  <form class="form-horizontal" method="post">
    <fieldset class="disable">
      <legend>Ethernet Interface</legend>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="eth">Interface</label>
        <div class="col-sm-10">
          <label class="radio-inline">
            <input type="radio" name="eth" value="enabled"
                   <?php echo ($eth) ? 'checked' : ''?>/>Enabled
          </label>
          <label class="radio-inline">
            <input type="radio" name="eth" value="disabled"
                   <?php echo (!$eth) ? 'checked' : ''?>/>Disabled
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="eth_dhcp">DHCP</label>
        <div class="col-sm-10">
          <label class="radio-inline">
            <input type="radio" name="eth_dhcp" value="enabled"
                   <?php echo $eth_disabled; ?>
                   <?php echo ($eth_dhcp) ? 'checked' : ''?>/>Enabled
          </label>
          <label class="radio-inline">
            <input type="radio" name="eth_dhcp" value="disabled"
                   <?php echo $eth_disabled; ?>
                   <?php echo (!$eth_dhcp) ? 'checked' : ''?>/>Disabled
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="eth_static_address">Static Address</label>
        <div class="col-sm-10">
          <input type="text" name="eth_static_address" class="form-control"
                 placeholder="IP Address" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 <?php echo $eth_dhcp_disabled; ?>
                 value="<?php echo $c->get('eth_static_address', ''); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="eth_static_netmask">Netmask</label>
        <div class="col-sm-10">
          <input type="text" name="eth_static_netmask" class="form-control"
                 placeholder="Netmask" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 <?php echo $eth_dhcp_disabled; ?>
                 value="<?php echo $c->get('eth_static_netmask', ''); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="eth_static_gateway">Gateway</label>
        <div class="col-sm-10">
          <input type="text" name="eth_static_gateway" class="form-control"
                 placeholder="Gateway Address" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 <?php echo $eth_dhcp_disabled; ?>
                 value="<?php echo $c->get('eth_static_gateway', ''); ?>"/>
        </div>
      </div>
    </fieldset>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="reset" class="btn">Reset</button>
    </div>
  </form>
</div>

<script type="text/javascript">
  // Enable/disable static input fields when DHCP is disabled/enabled
  $("input[name='eth_dhcp'][value='enabled']").click(function() { enable_dhcp('eth'); });
  $("input[name='eth_dhcp'][value='disabled']").click(function() { disable_dhcp('eth'); });

  // Enable/disable all input fields when interface is enabled/disabled
  $("input[name='eth'][value='enabled']").click(function() { enable_iface('eth'); });
  $("input[name='eth'][value='disabled']").click(function() { disable_iface('eth'); });

  function enable_dhcp(iface) {
    $(":input[name^='" + iface + "_static_']").attr('disabled', 'disabled');
  }

  function disable_dhcp(iface) {
    $(":input[name^='" + iface + "_static_']").removeAttr('disabled');
  }

  function enable_iface(iface) {
    $(":input[name^='" + iface + "_']").removeAttr('disabled');
    if ($("input[name='" + iface + "_dhcp']:checked").val() == 'enabled') {
      enable_dhcp(iface);
    } else {
      disable_dhcp(iface);
    }
  }

  function disable_iface(iface) {
    $(":input[name^='" + iface + "_']").attr('disabled', 'disabled');
  }
</script>

<?php include 'footer.inc.php'; ?>
