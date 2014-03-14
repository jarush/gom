<?php

class GarageDoor {
  private $gomClient;
  private $name;
  private $number;

  public function __construct($gomClient, $name, $number) {
    $this->gomClient = $gomClient;
    $this->name = $name;
    $this->number = $number;
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
      if ($name != null) {
        $garageDoors[] = new GarageDoor($gomClient, $name, $i);
      }
    }

    return $garageDoors;
  }
}

?>
