<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB, созданный из спарсенного содержимого
 */

class GoodsFromShop extends Goods {

  public function __construct($uri) {
    parent::__construct();
    $this->setProp('Origin goods url', $uri);

    $plugins =& $this->config->shopPolicy->plugins;
    if(!isset($plugins) || !is_object($plugins)) throw new Exception("Shop policy plugins not found");
    $this->loadPlugins(PFactory::getDir().'plugins/', $plugins);
    $this->applyShopPolicy('before_parse');

  }
  

  public function save() {
    $this->applyShopPolicy('after_parse');

    $mongo = new MongoClient();
    $collection = $mongo->{$this->config->mongo->db}->{$this->config->mongo->collection};
    $data = $collection->find()->sort(array('id' => -1))->getNext();
    if(isset($data['id'])) {
      $this->goods->data['Product id'] = $data['id']+1;
    } else {
      $this->goods->data['Product id'] = 1;
    }
    parent::save();
  }

}