<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB, созданный из записи Mongo
 */

class ExistsGoods extends Goods {

  public function __construct(stdClass $goodsData) {
    parent::__construct($goodsData);
  }

}