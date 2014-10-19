<?php
/**
 *  @package Parser
 */

/**
 *  Парсер html интернет-магазина
 */

class AliGoodsParser {

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
    if(isset($this->fields->{$varName})) return $this->fields->{$varName};
    switch($varName) {
      case 'price':
        $this->fields->{$varName} = $this->parsePrice();
        break;
      case 'name':
        $this->fields->{$varName} = $this->parseName();
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
    if (preg_match('~itemprop="name">(.*)<\/~Ui', $this->data, $matches)) {
      return $matches[1];
    } else {
      throw new Exception("Can not parse name");
    }
  }

  private function parseImages() {
    if (preg_match('~window\.runParams\.imageBigViewURL=\[.*~Ui', $this->data, $matches)) {
      //if (preg_match('~window\.runParams\.imageBigViewURL=(\[.*\]);~Ui', $this->data, $matches)) {
      
      die(var_dump($matches));
      $data = json_decode($matches[1]);
      
      $item = $data[0]; // TODO other items
      // Цена при акции, может не быть
      return (isset($item->skuVal->actSkuMultiCurrencyCalPrice)) ? $item->skuVal->actSkuMultiCurrencyCalPrice : null;
    }

    die('fail'.PHP_EOL);

  }

  private function parseActionPrice(){
    if (preg_match('~var skuProducts=(\[\{.*\}\]);~Ui', $this->data, $matches)) {
      $data = json_decode($matches[1]);
      $item = $data[0]; // TODO other items
      // Цена при акции, может не быть
      return (isset($item->skuVal->actSkuMultiCurrencyCalPrice)) ? $item->skuVal->actSkuMultiCurrencyCalPrice : null;
    }
    return null;
  }
  
  private function parsePrice() {
    if (preg_match('~var skuProducts=(\[\{.*\}\]);~Ui', $this->data, $matches)) {
      $data = json_decode($matches[1]);
      $item = $data[0]; // TODO other items
      // Цена товара
      return $item->skuVal->skuMultiCurrencyCalPrice;
    } else {
      throw new Exception("Can not parse price");
    }
    return $ret;
  }
  

}

      // if(isset($data[0]->skuVal->availQuantity)) echo $data[0]->skuVal->availQuantity . PHP_EOL; // может не быть доступное количество
      // echo $data[0]->skuVal->inventory . PHP_EOL; // количество?
      // echo (int)$data[0]->skuVal->isActivity . PHP_EOL; // активность?
      //echo $data[0]->skuPropIds . PHP_EOL;
      // die(var_dump($ret));
