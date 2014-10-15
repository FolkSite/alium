<?php

class AliGoodsParser {
  
  public function __construct() {}
  
  private $data;
  
  public function getContent($uri) {
    $this->data = file_get_contents($uri);
    if ($this->data === false) throw new Exception("$uri not available");
    return $this;
  }
  
  public function parse() {
    $ret = new StdClass();
    if (preg_match('~var skuProducts=(\[\{.*\}\]);~Ui', $this->data, $matches)) {
      $data = json_decode($matches[1]);
      $item = $data[0]; // TODO other items
      
      // Цена при акции, может не быть
      if(isset($item->skuVal->actSkuMultiCurrencyCalPrice)) $ret->{'Action price'} = $item->skuVal->actSkuMultiCurrencyCalPrice;
      
      // Цена товара
      $ret->{'Price'} = $item->skuVal->skuMultiCurrencyCalPrice;

      // if(isset($data[0]->skuVal->availQuantity)) echo $data[0]->skuVal->availQuantity . PHP_EOL; // может не быть доступное количество
      // echo $data[0]->skuVal->inventory . PHP_EOL; // количество?
      // echo (int)$data[0]->skuVal->isActivity . PHP_EOL; // активность?
      //echo $data[0]->skuPropIds . PHP_EOL;
      // die(var_dump($ret));
      
    } else {
      throw new Exception("Can not parse");
    }
    return $ret;
  }
  

}