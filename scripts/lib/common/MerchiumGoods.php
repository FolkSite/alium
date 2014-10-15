<?php

/**
 * @package Parser
 */

/**
 * Документ по типу ActiveRecord с сохранением в MongoDB
 */


class MerchiumGoods {
  
  public function __construct(stdClass $config) {
    $this->config = $config;
  }
  
  private $goods;
  public function setData($goodsData) {
    $this->goods = new StdClass();
    $this->goods->data = $goodsData;
    $this->parseCommonFields();
    return $this;
  }
  
  public function prepareData($plugins) {
    $this->parseFields($plugins);
    return $this;
  }
  
  public function loadGood($data) {
    $this->goods = $data;
    unset($this->goods->_id);
  }
  
  public function save() {
    $mongo = new MongoClient();
    $dbmongo = $mongo->{$this->config->dbname};
    $dbmongo->merchium->update(array('id' => $this->goods->data['Product id']), array('$set' => $this->goods), array('upsert'=>true));
  }

  public function updatePrice($newPrice) {
    $this->goods->{'Merchium price history'}[] = $this->goods->data['Price'];
    $this->goods->{'Origin price history'}[] = $newPrice;
    $this->goods->data['Price'] = $this->calcMarginPrice($newPrice);
  }
  
  public function calcMarginPrice($originPrice) {
   return $originPrice * 1.3;
  }

  public function getData() {
    return $this->goods->data;
  }

  public function getHeader() {
    return array_keys($this->goods->data);
  }
  
  private function parseFields($plugins) {
    if(count($plugins) > 0) foreach($plugins as $plugin) {
      call_user_func($plugin, $this->goods);
    }
  }
  
  private function parseCommonFields() {
    $this->goods->id = $this->goods->data['Product id'];
    // $this->goods->{'Shop price'} = $this->goods->data['Price'];
    // $this->goods->{'Shop status'} = $this->goods->data['Status'];
  }
  


}