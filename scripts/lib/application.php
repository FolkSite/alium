<?php

/**
 * @package Сore
 */

/**
 * Приложение
 */

class PApplication {

  /**
   * Init common classes
   *
   */

  public function init() 
  {
    require('core/factory.php');
    PFactory::init();
  }
}