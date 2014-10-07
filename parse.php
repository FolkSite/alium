<?php



class MerchiumGoodParser implements Iterator, Countable {
  
  private $position = 0, $fileName, $header = array(), $data = array(), $plugins = array();
  const DELIMITER = ';';
  
  public function __construct($fileName) {
    if(!file_exists($fileName)) throw new Exception("File $fileName not exists!");
    $this->fileName = $fileName;
    $this->sliceData();
  }
  
  public function count() { return count($this->data); } 
  
  public function rewind() { $this->position = 0; }
  
  public function key() { return $this->position; }

  public function next() { ++$this->position; }

  public function valid() { return isset($this->data[$this->position]); }
  
  public function current() { 
    $ret = $this->header;
    $data = $this->data[$this->position];
    foreach($ret as $keyName => $keyId) {
      $ret[$keyName] = $data[$keyId];
    }
    foreach($this->plugins as $plugin) {
      $ret = call_user_func($plugin, $ret);
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
  
  public function addPlugin(closure $plugin) {
    $this->plugins[] = $plugin;
  }
  
}

$linkFinder = function($data){
  $ret = $data;
  preg_match('~T\[(.*)\]~ui', $data['Features'], $mathes);
  $ret['Origin goods url'] = $mathes[1];
  return $ret;
};

$merchiumGoodParser = new MerchiumGoodParser('data/products_general_10072014.csv');
$merchiumGoodParser->addPlugin($linkFinder);

if(count($merchiumGoodParser) > 0) foreach ($merchiumGoodParser as $id => $good) {
  echo $id.PHP_EOL;
  echo var_dump($good);
}