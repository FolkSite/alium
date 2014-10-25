<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB
 */

abstract class Goods {
  use Plugin;
  
  private $goods, $config;
  public function __construct($goods = null) {
    if(!isset($goods)) {
      $this->goods = new StdClass();
      $this->goods->data = array();
    } else {
      $this->goods = $goods;
      unset($this->goods->_id);
    }
    $this->config = PApplication::getConfig();
  }

  public function getInstance($goodsData) {
    switch(gettype($goodsData)) {
      case 'array':
        PFactory::load('NewGoods');
        return new NewGoods($goodsData);
      case 'object':
        PFactory::load('ExistsGoods');
        return new ExistsGoods($goodsData);
      default:
        throw new Exception("No such type of instance");
    }
  }

  public function __get($name) {
    switch($name) {
      case 'Product id':
        return (int)$this->goods->data['Product id'];
      default:
        return isset($this->goods->data[$name]) ? $this->goods->data[$name] : null;
    }
  }

  public function __set($name, $value) {
    switch($name) {
      case 'Product id':
        $this->goods->data['Product id'] = (int)$value;
        $this->goods->id = (int)$value;
        break;
      default:
        $this->goods->data[$name] = $value;
    }
  }

  public function setProp($name, $value) {
    switch($name) {
      case 'id':
        $this->goods->data['Product id'] = (int)$value;
        $this->goods->id = (int)$value;
        break;
      default:
        $this->goods->$name = $value;
    }
  }

  public function getProp($name) {
    switch($name) {
      case 'id':
        return (int)$this->goods->data['Product id'];
      default:
        return isset($this->goods->$name) ? $this->goods->$name : null;
    }
  }

  public function __toString() {
    $ret = '';
    foreach($this->goods->data as $key => $_val) {
      $val = is_array($_val) ? implode(', ', $_val) : $_val;
      $ret .= $key.': '.$val.PHP_EOL;
    }
    return $ret;
  }

  public function _() {
    echo var_dump($this->goods);
  }

  /**
   *  Сохранение может быть переопределено в дочерних классах!
   */

  public function save() {
    $collection = $this->getCollection();
    $collection->update(array('id' => $this->getProp('id')), array('$set' => $this->goods), array('upsert'=>true));
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

  private static $priceFields = array('Product code', 'Language', 'Product id', 'Price', 'Status');

  public function getHeaderPrice() {
    return self::$priceFields;
  }

  public function getDataPrice() {
    $ret = array();
    foreach(self::$priceFields as $field) $ret[$field] = isset($this->goods->data[$field]) ? $this->goods->data[$field] : '';
    return $ret;
  }

  protected function getConfig() {
    return $this->config;
  }

  private static $collection;
  protected function getCollection() {
    if(isset(self::$collection)) return self::$collection;
    $mongo = new MongoClient();
    self::$collection = $mongo->{$this->config->mongo->db}->{$this->config->mongo->collection};
    return self::$collection;
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