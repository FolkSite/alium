<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB
 */

abstract class Goods {
  use Plugin;
  
  protected $goods, $config;
  public function __construct() {
    $this->config = PApplication::getConfig();
    $this->goods = new StdClass();
    $this->goods->data = array();
  }

  public function __get($name) {
    $isPropertyPresent = isset($this->goods->data[$name]);
    if ($isPropertyPresent) return $this->goods->data[$name];
    return null;
  }

  public function __set($name, $value) {
    $isPropertyPresent = isset($this->goods->data[$name]);
    $this->goods->data[$name] = $value;
  }

  public function setProp($name, $value) {
    $this->goods->$name = $value;
  }

  public function __toString() {
    $ret = '';
    foreach($this->goods->data as $key => $_val) {
      $val = is_array($_val) ? implode(', ', $_val) : $_val;
      $ret .= $key.': '.$val.PHP_EOL;
    }
    return $ret;
  }

  /**
   *  Сохранение может быть переопределено в дочерних классах!
   */

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

  private static $fields = array(
    'Product code', 'Language', 'Product id', 'Category', 'List price', 'Price', 'Status', 'Quantity', 'Weight', 
    'Min quantity', 'Max quantity', 'Quantity step', 'List qty count', 'Shipping freight', 'Date added', 'Downloadable', 
    'Files', 'Ship downloadable', 'Inventory tracking', 'Out of stock actions', 'Free shipping', 'Feature comparison', 
    'Zero price action', 'Thumbnail', 'Detailed image', 'Product name', 'Description', 'Short description', 'Meta keywords', 
    'Meta description', 'Search words', 'Page title', 'Taxes', 'Features', 'Options', 'Secondary categories', 'Product URL', 
    'Image URL', 'Detailed image URL', 'Items in box', 'Box size', 'Store', 'SEO name', 
    'YM Brand', 'YM Country of origin', 'YM Allow retail store purchase', 'YM Allow booking and self delivery', 
    'YM Allow delivery', 'YM Allow local delivery cost', 'YM Export Yes', 'YM Basic bid', 'YM Card bid', 'YM Model', 
    'YM Sales notes', 'YM typePrefix', 'YM Market category', 
    'TM Brand', 'TM Model', 'TM typePrefix', 'TM Allow local delivery cost', 'TM Allow delivery', 
    'TM Allow booking and self delivery', 'TM MCP', 'TM Export Yes', 'YM Manufacturer warranty', 'YM Seller warranty'
  );

  public function getHeader() {
    return self::$fields;
  }

  public function getData() {
    $ret = array();
    foreach(self::$fields as $field) $ret[$field] = isset($this->goods->data[$field]) ? $this->goods->data[$field] : '';
    return $ret;
  }

  public function isAllFields() {
    foreach(self::$fields as $field) {
      if (!isset($this->goods->data[$field])) return false;
    }
    return true;
  }

  protected function prepareCommonFields() {
    $this->goods->id = (int)$this->goods->data['Product id'];
    $this->goods->src = 'Ali';
  }
  
  protected function applyShopPolicy($section) {
    if(!isset($this->plugins[$section])) return;
    foreach($this->plugins[$section] as $plugin) {
      call_user_func($plugin, $this->goods);
    }
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