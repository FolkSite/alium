<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB, созданный из спарсенного содержимого
 */

class GoodsFromShop extends Goods {

  public function __construct() {
    parent::__construct();

    $plugins =& $this->config->shopPolicy->plugins;
    if(!isset($plugins) || !is_object($plugins)) throw new Exception("Shop policy plugins not found");
    $this->loadPlugins(PFactory::getDir().'plugins/', $plugins);
    $this->applyShopPolicy('before_parse');
  }

  public function save() {
    $this->applyShopPolicy('after_parse');
    // parent::save();
  }

}