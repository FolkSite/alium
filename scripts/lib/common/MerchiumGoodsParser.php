<?php

class MerchiumGoodsParser implements Iterator, Countable {
  
  private $position = 0, $fileName, $header = array(), $data = array(), $plugins = array(), $config;
  const DELIMITER = ';';
  
  public function __construct($fileName, $config) {
    if(!file_exists($fileName)) throw new Exception("File $fileName not exists!");
    $this->config = $config;
    $this->fileName = $fileName;
    $this->sliceData();
  }
  
  public function count() { return count($this->data); } 
  
  public function rewind() { $this->position = 0; }
  
  public function key() { return $this->position; }

  public function next() { ++$this->position; }

  public function valid() { return isset($this->data[$this->position]); }
  
  public function current() { 
    $goodData = $this->header;
    $data = $this->data[$this->position];
    foreach($goodData as $keyName => $keyId) {
      $goodData[$keyName] = $data[$keyId];
    }
    $merchiumGoods = new MerchiumGoods($this->config);
    $merchiumGoods->setData($goodData)->prepareData($this->plugins);
    return $merchiumGoods;
  }

  public function addPlugin(closure $plugin) {
    $this->plugins[] = $plugin;
  }
  
  public function __toString() {
    $ret = '';
    foreach($this as $good) {
      $ret .= "{$good['Product id']} >> {$good['Product name']} [{$good['Price']}]".PHP_EOL;
    }
    return $ret;
  }
  
  private function getRawData() {
    return file($this->fileName);
  }
  
  private function getHeader($rawData) {
    $this->header = array_flip(str_getcsv($rawData[0], self::DELIMITER));
  }  
  
  private function getData($rawData) {
    foreach(array_slice($rawData, 1) as $line) {
      $data = str_getcsv($line, self::DELIMITER);
      if(count($data) != count($this->header)) throw new Exception("Wrong file format in $fileName!");
      $this->data[] = $data;
    }
  }
  
  private function sliceData() {
    $rawData = $this->getRawData();
    if(count($rawData) < 2) throw new Exception("Wrong file format in $fileName!");
    $this->getHeader($rawData);
    $this->getData($rawData);
  }
  

  
}