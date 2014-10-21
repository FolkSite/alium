<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB, созданный из переданного массива
 */

class GoodsFromArray extends Goods {

  public function __construct($goodsData) {
    parent::__construct();
    
    $this->goods = new StdClass();
    $this->goods->data = $goodsData;
    $this->prepareCommonFields();
  }
  
  public function prepareData($plugins) {
    $this->parseFields($plugins);
    return $this;
  }
  
  private function parseFields($plugins) {
    if(is_array($plugins['csv_prepare']) && count($plugins['csv_prepare']) > 0) foreach($plugins['csv_prepare'] as $plugin) {
      call_user_func($plugin, $this->goods);
    }
  }
  

}