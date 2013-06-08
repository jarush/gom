<?php

class GpioClient {
  private $fp;

  public function __construct($address, $port) {
    $this->fp = fsockopen($address, $port);
    if (!$this->fp) {
      throw new Exception('Failed to open socket');
    }
  }

  function __destruct() {
    $this->close();
  }

  public function close() {
    fclose($this->fp);
  }

  public function sendMessage($message) {
    fputs($this->fp, $message . "\n");

    return fgets($this->fp);
  }

  public function getGpio($gpio) {
    $response = $this->sendMessage("get $gpio");

    if (strpos($response, 'ERROR', 0) === 0) {
      throw new Exception($response);
    }

    return $response;
  }

  public function setGpio($gpio, $value) {
    $response = $this->sendMessage("set $gpio $value");
    if (strpos($response, 'ERROR', 0) === 0) {
      throw new Exception($response);
    }
  }

  public function toggleGpio($gpio, $value, $duration) {
    $response = $this->sendMessage("toggle $gpio $value $duration");
    if (strpos($response, 'ERROR', 0) === 0) {
      throw new Exception($response);
    }
  }
}

?>
