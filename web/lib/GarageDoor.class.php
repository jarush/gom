<?php

class GarageDoor {
  private $gomClient;
  private $number;
  private $name;
  private $sensorGpio;
  private $relayGpio;

  public function __construct($gomClient, $number, $name,
      $sensorGpio, $relayGpio) {
    $this->gomClient = $gomClient;
    $this->name = $name;
    $this->number = $number;
    $this->sensorGpio = $sensorGpio;
    $this->relayGpio = $relayGpio;
  }

  function __destruct() {
    if ($this->gomClient != null) {
      $this->gomClient->close();
    }
  }

  public function getName() {
    return $this->name;
  }

  public function getNumber() {
    return $this->number;
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

    $config = new Config('gomd.properties');

    for ($i = 0; $i < 10; $i++) {
      $name = $config->get('door' . $i . '.name', null);
      $sensorGpio = $config->get('door' . $i . '.sensor.gpio', null);
      $relayGpio = $config->get('door' . $i . '.relay.gpio', null);

      if ($name == null || $sensorGpio == null || $relayGpio == null) {
        continue;
      }

      $garageDoors[] = new GarageDoor($gomClient, $i, $name,
        $sensorGpio, $relayGpio);
    }

    return $garageDoors;
  }
}

?>
