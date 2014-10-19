<?php

/**
 *  Приложение
 */

/**
 *  Приложение
 *  @package Сore
 */

class PApplication {

  /**
   * Init common classes
   *
   */

  public static function init() 
  {
    require('core/factory.php');
    PFactory::init();
  }

  private static $config;
  public static function loadConfig($fileName) {
    if(!file_exists($fileName)) throw new Exception("Can not access to config $fileName");
    self::$config = json_decode(file_get_contents($fileName));
    if(!is_object(self::$config)) throw new Exception("Config $fileName is invalid");
  }

  public static function getConfig() {
    return self::$config;
  }
}