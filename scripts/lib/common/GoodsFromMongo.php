<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB, созданный из записи Mongo
 */

class GoodsFromMongo extends Goods {

  public function __construct($goodsData) {
      parent::__construct();
      
      $this->goods = $goodsData;
      unset($this->goods->_id);
  }

  public function getHeader() {
    return array_keys($this->goods->data);
  }

  public function getData() {
    return $this->goods->data;
  }

}