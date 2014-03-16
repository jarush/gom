<?php

class NetworkInterface {
  private $name;
  private $addressFamily;
  private $method;

  private $options;
  private $auto;

  public function __construct() {
    $this->options = array();
    $this->auto = FALSE;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getAddressFamily() {
    return $this->addressFamily;
  }

  public function setAddressFamily($addressFamily) {
    $this->addressFamily = $addressFamily;
  }

  public function getMethod() {
    return $this->method;
  }

  public function setMethod($method) {
    $this->method = $method;
  }

  public function getOptions() {
    return $this->options;
  }

  public function getOption($name) {
    return $this->options[$name];
  }

  public function setOption($name, $value) {
    $this->options[$name] = $value;
  }

  public function isAuto() {
    return $this->auto;
  }

  public function setAuto($auto) {
    $this->auto = $auto;
  }

  public static function LoadNetworkInterfaces($filename) {
    $interfaces = array();

    $fh = fopen($filename, 'r');
    if ($fh === FALSE) {
      return FALSE;
    }

    $interface = null;
    while (($line = fgets($fh, 4096)) !== false) {
      // Trim off any whitespace
      $line = trim($line);

      // Skip blank and comment lines
      if ($line == '' || $line[0] == '#') {
        continue;
      }

      // Break up the line into tokens
      $tokens = explode(' ', $line);

      // Parse the line of supported values
      switch ($tokens[0]) {
        case 'iface':
          if (!isset($interfaces[$tokens[1]])) {
            $interface = new NetworkInterface();
            $interfaces[$tokens[1]] = $interface;
          } else {
            $interface = $interfaces[$tokens[1]];
          }

          $interface->setName($tokens[1]);
          $interface->setAddressFamily($tokens[2]);
          $interface->setMethod($tokens[3]);
          break;

        case 'auto':
          if (!isset($interfaces[$tokens[1]])) {
            $interface = new NetworkInterface();
            $interfaces[$tokens[1]] = $interface;
          } else {
            $interface = $interfaces[$tokens[1]];
          }
          $interface->setAuto(TRUE);
          break;

        // Unsupported "stanzas"
        case 'allow-hotplug':
        case 'mapping':
        case 'source':
          $interface = null;
          break;

        // Everything else are options
        default:
          if ($interface != null) {
            $value = implode(' ', array_slice($tokens, 1));
            $interface->setOption($tokens[0], $value);
          }
          break;
      }
    }

    return $interfaces;
  }

  public static function SaveNetworkInterfaces($filename, $interfaces) {
    $fh = fopen($filename, 'w');
    if ($fh === FALSE) {
      return FALSE;
    }

    foreach ($interfaces as $interface) {
      if ($interface->isAuto() === TRUE) {
        fwrite($fh, "auto " . $interface->getName() . "\n");
      }

      fwrite($fh, "iface " . $interface->getName() . " " .
          $interface->getAddressFamily() . " " . $interface->getMethod() . "\n");

      foreach ($interface->getOptions() as $name => $value) {
        fwrite($fh, "  $name $value\n");
      }
      fwrite($fh, "\n");
    }

    fclose($fh);

    return TRUE;
  }
}

?>
