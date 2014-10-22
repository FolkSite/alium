<?php
/**
 *  @package Parser
 */

/**
 *  Парсер html интернет-магазина
 */

class AliGoodsParser extends Parser {

  /**
   *  @internal
   */
  
  public function __construct() {}
  
  private $data, $fields;

  public function getContent($uri) {
    $this->fields = new StdClass();
    $this->data = file_get_contents($uri);
    if ($this->data === false) throw new Exception("`$uri` not available");
    return $this;
  }

  public function __get($varName) {
    $value =& $this->fields->{$varName};
    if(isset($value)) return $value;
    $plugin =& $this->plugins['parsing'][$varName];
    if (!isset($plugin)) throw new Exception("Can not parse `$varName` property. Add this plugin!");
    $value = $plugin($this->data);
    return $value;
  }


}