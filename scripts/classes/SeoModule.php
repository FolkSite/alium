<?php

/**
 *  @package Seo
 */

/**
 *  SEO-модуль
 */

class SeoModule extends Parser {

  private $morphology, $fields;
  public function __construct() {
    PFactory::load('Morphology');
    $this->morphology = Morphology::_();
    $this->fields = new StdClass();
  }

  public function __get($varName) {
    $value =& $this->fields->{$varName};
    if(isset($value)) return $value;
    $plugin =& $this->plugins['parsing'][$varName];
    if (!isset($plugin)) throw new Exception("Can not parse `$varName` property. Add this plugin!");
    $value = $plugin($this);
    return $value;
  }

  public function getMorphology() {
    return $this->morphology;
  }

  public function setText($text) {
    $this->morphology->setText($text);
  }


}