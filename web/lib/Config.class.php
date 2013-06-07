<?php

class Config {
  private $ini;

  public function __construct($filename) {
    $this->ini = parse_ini_file(APP_ROOT . '/etc/' . $filename, false, INI_SCANNER_RAW);
  }

  public function get($name, $default) {
    if (empty($this->ini[$name])) {
      return $default;
    }

    return $this->ini[$name];
  }

  public function set($name, $value) {
    $this->ini[$name] = $value;
  }

  public function save($filename) {
    $fh = fopen(APP_ROOT . '/etc/' . $filename, 'w');
    if ($fh === FALSE) {
      return FALSE;
    }

    foreach ($this->ini as $key => $value) {
      if (fwrite($fh, "$key=$value\n") === FALSE) {
        fclose($fh);
        return FALSE;
      }
    }

    fclose($fh);

    return TRUE;
  }
}

?>
