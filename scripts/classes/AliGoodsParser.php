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
  
  private $data;

  public function getContent($uri) {
    $this->fields = new StdClass();
    $this->data = file_get_contents($uri);
    if ($this->data === false) throw new Exception("$uri not available");
    return $this;
  }

  public function __get($varName) {
    $value =& $this->fields->{$varName};
    if(isset($value)) return $value;
    $plugin =& $this->plugins['parsing'][$varName];
    if (!isset($plugin)) throw new Exception("Can not parse $varName property. Add this plugin!");
    $value = $plugin($this->data);
    return $value;
    
    
    switch($varName) {
      case 'price':
        $this->fields->{$varName} = $this->parsePrice();
        break;
      case 'name':
        $this->fields->{$varName} = $this->parseName();
        break;      
      case 'weight':
        $this->fields->{$varName} = $this->parseWeight();
        break;
      case 'images':
        $this->fields->{$varName} = $this->parseImages();
        break;
      default:
        return null;
    }
    return $this->fields->{$varName};
  }

  private function parseName() {
    if (preg_match('~itemprop="name">(.*)<\/~Usi', $this->data, $matches)) {
      return $matches[1];
    } else {
      throw new Exception("Can not parse name");
    }
  }

  private function parseImages() {
    if (preg_match('~window\.runParams\.imageBigViewURL=(\[.*\]);~Usi', $this->data, $matches)) {
      $data = json_decode($matches[1]);
      return $data;
    }
    return array();
  }

  private function parseActionPrice() {
    if (preg_match('~var skuProducts=(\[\{.*\}\]);~Usi', $this->data, $matches)) {
      $data = json_decode($matches[1]);
      $item = $data[0]; // TODO other items
      // Цена при акции, может не быть
      return (isset($item->skuVal->actSkuMultiCurrencyCalPrice)) ? $item->skuVal->actSkuMultiCurrencyCalPrice : null;
    }
    return null;
  }
  

}

      // if(isset($data[0]->skuVal->availQuantity)) echo $data[0]->skuVal->availQuantity . PHP_EOL; // может не быть доступное количество
      // echo $data[0]->skuVal->inventory . PHP_EOL; // количество?
      // echo (int)$data[0]->skuVal->isActivity . PHP_EOL; // активность?
      //echo $data[0]->skuPropIds . PHP_EOL;
      // die(var_dump($ret));
