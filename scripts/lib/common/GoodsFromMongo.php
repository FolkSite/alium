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

}