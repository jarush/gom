<?php

class GarageDoor {
  private $gpioClient;
  private $sensorGpio;
  private $relayGpio;

  public function __construct($gpioClient, $sensorGpio, $relayGpio) {
    $this->gpioClient = $gpioClient;
    $this->sensorGpio = $sensorGpio;
    $this->relayGpio = $relayGpio;
  }

  function __destruct() {
    $this->gpioClient->close();
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
}

?>
