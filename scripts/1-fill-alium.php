<?php

require('lib/application.php');
PApplication::init();

class GoodsFiller extends Cli {

  const MANUAL = 'Use {app} project.json file.csv';

  public function __construct() {
    PFactory::load('MerchiumGoodsParser');
    PFactory::load('MerchiumGoods');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 3) die($this->showMan());
    $configFile = $argv[1];
    $dataFile = $argv[2];
    
    $this->loadConfig($configFile)->fill($dataFile);
  }
  
  private $merchiumGoodsParser;
  private function fill($fileName) {

    $this->merchiumGoodsParser = new MerchiumGoodsParser($fileName, $this->config->mongo);
    $this->loadPlugins('plugins.json');
    
    if(count($this->merchiumGoodsParser) > 0) foreach ($this->merchiumGoodsParser as $id => $good) {
      $good->save();
    }
  }

  private function loadPlugins($fileName) {
    $config = json_decode(file_get_contents(PFactory::getDir()."plugins/$fileName"));
    if(!is_object($config)) throw new Exception("Plugins config $fileName is invalid");
    if(count($config->plugins) > 0) foreach($config->plugins as $plugin) {
      require(PFactory::getDir()."plugins/{$plugin->file}");
      $this->merchiumGoodsParser->addPlugin($plugin);
    }
    return $this;
  }


}

$goodsFiller = new GoodsFiller();
$goodsFiller->run();