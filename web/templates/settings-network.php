<?php
include 'header.inc.php';
include 'settings-tabs.inc.php';
include 'funcs.inc.php';

$interfaces = NetworkInterface::LoadNetworkInterfaces("/etc/network/interfaces");

$interface = $interfaces['eth0'];
if (isset($interfaces)) {
  $ethDhcp = $interface->getMethod() == 'dhcp';
  $ethStaticAddress = $interface->getOption('address');
  $ethStaticNetmask = $interface->getOption('netmask');
  $ethStaticGateway = $interface->getOption('gateway');
}

?>

<div class="tab-body">
  <?php
    if (isset($status)) {
      showAlert($status, $message);
    }
  ?>

  <form class="form-horizontal" method="post">
    <legend>Ethernet Interface</legend>

    <div class="form-group">
      <label class="col-sm-3 control-label" for="ethDhcp">DHCP</label>
      <div class="col-sm-9">
        <label class="radio-inline">
          <input type="radio" name="ethDhcp" value="enabled"
                 <?php echo ($ethDhcp) ? 'checked="checked"' : ''?>/>Enabled
        </label>
        <label class="radio-inline">
          <input type="radio" name="ethDhcp" value="disabled"
                 <?php echo (!$ethDhcp) ? 'checked="checked"' : ''?>/>Disabled
        </label>
      </div>
    </div>

    <fieldset id="ethStaticFields"
              <?php echo ($ethDhcp) ? 'disabled="disabled"' : ''; ?>>
      <div class="form-group">
        <label class="col-sm-3 control-label" for="ethStaticAddress">Static Address</label>
        <div class="col-sm-9">
          <input type="text" name="ethStaticAddress" class="form-control"
                 placeholder="IP Address" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 value="<?php echo $ethStaticAddress; ?>"/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label" for="ethStaticNetmask">Netmask</label>
        <div class="col-sm-9">
          <input type="text" name="ethStaticNetmask" class="form-control"
                 placeholder="Netmask" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 value="<?php echo $ethStaticNetmask; ?>"/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label" for="ethStaticGateway">Gateway</label>
        <div class="col-sm-9">
          <input type="text" name="ethStaticGateway" class="form-control"
                 placeholder="Gateway Address" required="required"
                 pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                 value="<?php echo $ethStaticGateway; ?>"/>
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
  $("input[name='ethDhcp']").click(function() {
    var disabled = $(this).val() == 'enabled';
    $("#ethStaticFields").prop('disabled', disabled);
  });
</script>

<?php include 'footer.inc.php'; ?>
