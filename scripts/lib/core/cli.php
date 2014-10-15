<?php
class Cli {

  protected $config;
  protected function loadConfig($fileName) {
    if(!file_exists($fileName)) throw new Exception("Can not access to config $fileName");
    $this->config = json_decode(file_get_contents($fileName));
    if(!is_object($this->config)) throw new Exception("Config $fileName is invalid");
    return $this;
  }

  protected function showMan() {
    die(static::MANUAL.PHP_EOL);
  }

}