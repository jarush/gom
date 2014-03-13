<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';

// Get the network configuration
$c = new Config('network.properties');

// Check if the wlan interface and wlan dhcp are enabled
$wlan = $c->get('wlan', null) == 'enabled';
$wlan_dhcp = $c->get('wlan_dhcp', null) == 'enabled';
if (!$wlan) {
  $wlan_disabled = 'disabled';
  $wlan_dhcp_disabled = 'disabled';
} else if ($wlan_dhcp) {
  $wlan_disabled = '';
  $wlan_dhcp_disabled = 'disabled';
} else {
  $wlan_disabled = '';
  $wlan_dhcp_disabled = '';
}

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

<?php
if (isset($status)) {
  echo '<div class="alert alert-success">';
  echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
  echo $status;
  echo '</div>';
}
?>

<div class="tab-body">
  <form class="form-horizontal" method="post">
    <fieldset>
      <legend>Wireless Interface</legend>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan">Interface</label>
        <div class="col-sm-10">
          <label class="radio-inline">
            <input type="radio" name="wlan" value="enabled"
                   <?php echo ($wlan) ? 'checked' : ''?>/>Enabled
          </label>
          <label class="radio-inline">
            <input type="radio" name="wlan" value="disabled"
                   <?php echo (!$wlan) ? 'checked' : ''?>/>Disabled
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan_ssid">SSID</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="wlan_ssid"
                 placeholder="SSID" required="required"
                 <?php echo $wlan_disabled; ?>
                 value="<?php echo $c->get('wlan_ssid', ''); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan_security_mode">Security Mode</label>
        <div class="col-sm-10">
          <select name="wlan_security_mode" class="form-control"
                  <?php echo $wlan_disabled; ?>>
          <?php
            $modes = array('WPA2 Personal', 'WPA Personal', 'WEP');
            $selected_mode = $c->get('wlan_security_mode', null);
            foreach ($modes as $mode) {
              $selected = $mode == $selected_mode ? 'selected' : '';
              echo "<option $selected>$mode</option>";
            }
          ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan_key">Key</label>
        <div class="col-sm-10">
          <input type="text" name="wlan_key" class="form-control"
                 placeholder="Key" required="required"
                 <?php echo $wlan_disabled; ?>
                 value="<?php echo $c->get('wlan_key', ''); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan_dhcp">DHCP</label>
        <div class="col-sm-10">
          <label class="radio-inline">
            <input type="radio" name="wlan_dhcp" value="enabled"
                 <?php echo $wlan_disabled; ?>
                 <?php echo ($wlan_dhcp) ? 'checked' : ''?>/>Enabled
          </label>
          <label class="radio-inline">
            <input type="radio" name="wlan_dhcp" value="disabled"
                 <?php echo $wlan_disabled; ?>
                 <?php echo (!$wlan_dhcp) ? 'checked' : ''?>/>Disabled
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan_static_address">Static Address</label>
        <div class="col-sm-10">
          <input type="text" name="wlan_static_address" class="form-control"
                 placeholder="IP Address" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 <?php echo $wlan_dhcp_disabled; ?>
                 value="<?php echo $c->get('wlan_static_address', ''); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan_static_netmask">Netmask</label>
        <div class="col-sm-10">
          <input type="text" name="wlan_static_netmask" class="form-control"
                 placeholder="Netmask" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 <?php echo $wlan_dhcp_disabled; ?>
                 value="<?php echo $c->get('wlan_static_netmask', ''); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="wlan_static_gateway">Gateway</label>
        <div class="col-sm-10">
          <input type="text" name="wlan_static_gateway" class="form-control"
                 placeholder="Gateway Address" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 <?php echo $wlan_dhcp_disabled; ?>
                 value="<?php echo $c->get('wlan_static_gateway', ''); ?>"/>
        </div>
      </div>
    </fieldset>

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
  $("input[name='wlan_dhcp'][value='enabled']").click(function() { enable_dhcp('wlan'); });
  $("input[name='wlan_dhcp'][value='disabled']").click(function() { disable_dhcp('wlan'); });
  $("input[name='eth_dhcp'][value='enabled']").click(function() { enable_dhcp('eth'); });
  $("input[name='eth_dhcp'][value='disabled']").click(function() { disable_dhcp('eth'); });

  // Enable/disable all input fields when interface is enabled/disabled
  $("input[name='wlan'][value='enabled']").click(function() { enable_iface('wlan'); });
  $("input[name='wlan'][value='disabled']").click(function() { disable_iface('wlan'); });
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
