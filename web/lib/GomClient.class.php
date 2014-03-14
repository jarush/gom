<?php

class GomClient {
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
    if ($this->fp) {
      fclose($this->fp);
      $this->fp = false;
    }
  }

  public function sendMessage($message) {
    fputs($this->fp, $message . "\n");

    $response = fgets($this->fp);

    return $response;
  }

  public function getStatus($door) {
    $response = $this->sendMessage("get $door");

    if (strpos($response, 'ERROR', 0) === 0) {
      throw new Exception($response);
    }

    return $response;
  }

  public function toggle($door) {
    $response = $this->sendMessage("toggle $door");
    if (strpos($response, 'ERROR', 0) === 0) {
      throw new Exception($response);
    }
  }
}

?>
