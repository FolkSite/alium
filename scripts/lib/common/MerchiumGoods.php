<?php

/**
 * @package Parser
 */

/**
 * Документ по типу ActiveRecord с сохранением в MongoDB
 */


class MerchiumGoods {
  
  private $config, $priceCalculator;
  public function __construct(stdClass $config) {
    $this->config = $config;
    PFactory::load('PriceCalculator');
    $this->priceCalculator = new PriceCalculator($this->config);
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
    $collection = $mongo->{$this->config->mongo->db}->{$this->config->mongo->collection};
    $collection->update(array('id' => $this->goods->data['Product id']), array('$set' => $this->goods), array('upsert'=>true));
  }

  public function updatePrice($newOriginPrice) {
    $newMerchiumPrice = $this->priceCalculator->calcMarginPrice($newOriginPrice); // !!!
    $this->historyOriginPrice($newOriginPrice);
    $this->historyMerchiumPrice($newMerchiumPrice);
    $this->goods->data['Price'] = $newMerchiumPrice;
  }

  private function historyOriginPrice($newOriginPrice) {
    $oldOriginPrice = isset($this->goods->{'Origin price history'}) ? end($this->goods->{'Origin price history'}) : false;
    if ($oldOriginPrice === false || $oldOriginPrice != $newOriginPrice) $this->goods->{'Origin price history'}[] = $newOriginPrice;
  }

  private function historyMerchiumPrice($newMerchiumPrice) {
    $oldMerchiumPrice = isset($this->goods->{'Merchium price history'}) ? end($this->goods->{'Merchium price history'}) : false;
    if ($oldMerchiumPrice === false || $oldMerchiumPrice != $newMerchiumPrice) $this->goods->{'Merchium price history'}[] = $newMerchiumPrice;
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