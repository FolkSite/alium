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
  }

  public function save() {
    $this->applyShopPolicy();
    // parent::save();
  }

  private function applyShopPolicy() {
    foreach($this->plugins['before_save'] as $plugin) {
      call_user_func($plugin, $this->goods);
    }
  }




}