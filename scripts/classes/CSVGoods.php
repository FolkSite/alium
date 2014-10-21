<?php

/**
 *  @package Parser
 */

/**
 *  Итерируемый набор товаров Goods
 */

class CSVGoods extends Parser implements Iterator, Countable {
  
  private $position = 0, $fileName, $header = array(), $data = array();
  const DELIMITER = ';';

  /**
   *  Создание итерируемого набора товаров Goods из файла CSV
   *  @param string $fileName путь к файлу
   */
  
  public function __construct($fileName) {
    if(!file_exists($fileName)) throw new Exception("File $fileName not exists!");
    PFactory::load('GoodsFromArray');
    $this->fileName = $fileName;
    $this->sliceData();
    $this->loadPluginsFromFile(PFactory::getDir().'plugins/fetch.json', 'plugins');
  }

  /**
   *  @internal
   */
  
  public function count() { return count($this->data); }

  /**
   *  @internal
   */
  
  public function rewind() { $this->position = 0; }

  /**
   *  @internal
   */
  
  public function key() { return $this->position; }

  /**
   *  @internal
   */

  public function next() { ++$this->position; }

  /**
   *  @internal
   */

  public function valid() { return isset($this->data[$this->position]); }

  /**
   *  @internal
   */
  
  public function current() {
    $goodData = $this->header;
    $data = $this->data[$this->position];
    foreach($goodData as $keyName => $keyId) {
      $goodData[$keyName] = $data[$keyId];
    }
    $goods = new GoodsFromArray($goodData);

    $goods->prepareData($this->plugins);
    return $goods;
  }

  /**
   *  @internal
   */
  
  public function __toString() {
    $ret = '';
    foreach($this as $good) {
      $ret .= "{$good['Product id']} >> {$good['Product name']} [{$good['Price']}]".PHP_EOL;
    }
    return $ret;
  }

  /**
   *  @internal
   */
  
  private function getRawData() {
    return file($this->fileName);
  }

  /**
   *  @internal
   */
  
  private function getHeader($rawData) {
    $this->header = array_flip(str_getcsv($rawData[0], self::DELIMITER));
  }

  /**
   *  @internal
   */
  
  private function getData($rawData) {
    foreach(array_slice($rawData, 1) as $line) {
      $data = str_getcsv($line, self::DELIMITER);
      if(count($data) != count($this->header)) throw new Exception("Wrong file format in $fileName!");
      $this->data[] = $data;
    }
  }

  /**
   *  @internal
   */
  
  private function sliceData() {
    $rawData = $this->getRawData();
    if(count($rawData) < 2) throw new Exception("Wrong file format in $fileName!");
    $this->getHeader($rawData);
    $this->getData($rawData);
  }
  

  
}