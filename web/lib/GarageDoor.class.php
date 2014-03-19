<?php

class GarageDoor {
  private $gomClient;
  private $number;
  private $enabled;
  private $name;
  private $sensorGpio;
  private $relayGpio;

  public function __construct($gomClient, $number, $enabled,
      $name, $sensorGpio, $relayGpio) {
    $this->gomClient = $gomClient;
    $this->number = $number;
    $this->enabled = $enabled;
    $this->name = $name;
    $this->sensorGpio = $sensorGpio;
    $this->relayGpio = $relayGpio;
  }

  function __destruct() {
    if ($this->gomClient != null) {
      $this->gomClient->close();
    }
  }

  public function getNumber() {
    return $this->number;
  }

  public function getEnabled() {
    return $this->enabled;
  }

  public function getName() {
    return $this->name;
  }

  public function getSensorGpio() {
    return $this->sensorGpio;
  }

  public function getRelayGpio() {
    return $this->relayGpio;
  }

  public function isClosed() {
    return $this->gomClient->getStatus($this->number) == 1;
  }

  public function getStatus() {
    return $this->isClosed() ? 'Closed' : 'Open';
  }

  public function pressButton() {
    $this->gomClient->toggle($this->number);
  }

  public static function LoadGarageDoors($gomClient) {
    $garageDoors = array();

    $config = new Config('/config/gomd.properties');

    for ($i = 0; $i < 3; $i++) {
      $enabled = $config->get('door' . $i . '.enabled', 'false') == 'true';
      $name = $config->get('door' . $i . '.name', '');
      $sensorGpio = $config->get('door' . $i . '.sensor.gpio', '');
      $relayGpio = $config->get('door' . $i . '.relay.gpio', '');

      $garageDoors[] = new GarageDoor($gomClient, $i, $enabled,
        $name, $sensorGpio, $relayGpio);
    }

    return $garageDoors;
  }
}

?>
