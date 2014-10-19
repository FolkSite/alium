<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB
 */

abstract class Goods {
  
  protected $goods, $config;
  public function __construct() {
    $this->config = PApplication::getConfig();
  }

  public function save() {
    $mongo = new MongoClient();
    $collection = $mongo->{$this->config->mongo->db}->{$this->config->mongo->collection};
    $collection->update(array('id' => $this->goods->data['Product id']), array('$set' => $this->goods), array('upsert'=>true));
  }

  public function updatePrice($newOriginPrice) {
    $newMerchiumPrice = $this->getCalc()->calcMarginPrice($newOriginPrice); // !!!
    $this->historyOriginPrice($newOriginPrice);
    $this->historyMerchiumPrice($newMerchiumPrice);
    $this->goods->data['Price'] = $newMerchiumPrice;
  }

  protected function prepareCommonFields() {
    $this->goods->id = $this->goods->data['Product id'];
    // $this->goods->{'Shop price'} = $this->goods->data['Price'];
    // $this->goods->{'Shop status'} = $this->goods->data['Status'];
  }

  private $priceCalculator;
  private function getCalc() {
    PFactory::load('PriceCalculator');
    $this->priceCalculator = new PriceCalculator($this->config);
    return $this->priceCalculator;
  }

  private function historyOriginPrice($newOriginPrice) {
    $oldOriginPrice = isset($this->goods->{'Origin price history'}) ? end($this->goods->{'Origin price history'}) : false;
    if ($oldOriginPrice === false || $oldOriginPrice != $newOriginPrice) $this->goods->{'Origin price history'}[] = $newOriginPrice;
  }

  private function historyMerchiumPrice($newMerchiumPrice) {
    $oldMerchiumPrice = isset($this->goods->{'Merchium price history'}) ? end($this->goods->{'Merchium price history'}) : false;
    if ($oldMerchiumPrice === false || $oldMerchiumPrice != $newMerchiumPrice) $this->goods->{'Merchium price history'}[] = $newMerchiumPrice;
  }
  
}