<?php

class GarageDoor {
  private $gpioClient;
  private $name;
  private $sensorGpio;
  private $relayGpio;

  public function __construct($gpioClient, $name, $sensorGpio, $relayGpio) {
    $this->name = $name;
    $this->gpioClient = $gpioClient;
    $this->sensorGpio = $sensorGpio;
    $this->relayGpio = $relayGpio;
  }

  function __destruct() {
    if ($this->gpioClient != null) {
      $this->gpioClient->close();
    }
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
    return $this->gpioClient->getGpio($this->sensorGpio) == 1;
  }

  public function getStatus() {
    return $this->isClosed() ? 'Closed' : 'Open';
  }

  public function pressButton() {
    $this->gpioClient->toggleGpio($this->relayGpio, 1, 250);
  }

  public static function loadGarageDoors($filename, $gpioClient) {
    $garageDoors = array();

    $config = new Config('garagedoors.properties');

    for ($i = 0; $i < 10; $i++) {
      $name = $config->get('name' . $i, null);
      $sensorGpio = $config->get('sensorGpio' . $i, null);
      $relayGpio = $config->get('relayGpio' . $i, null);

      // Skip the door if any information is missing
      if ($name == null || $sensorGpio == null || $relayGpio == null) {
        continue;
      }

      $garageDoors[] = new GarageDoor($gpioClient, $name, $sensorGpio, $relayGpio);
    }

    return $garageDoors;
  }
}

?>
