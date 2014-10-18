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

    $this->merchiumGoodsParser = new MerchiumGoodsParser($fileName, $this->config);
    $this->loadPlugins('plugins.json');
    
    $i = 0;
    if(count($this->merchiumGoodsParser) > 0) foreach ($this->merchiumGoodsParser as $id => $good) {
      $good->save();
      $i++;
    }
    echo "Goods processed: $i".PHP_EOL;

  }

  private function loadPlugins($fileName) {
    $pluginConfig = json_decode(file_get_contents(PFactory::getDir()."plugins/$fileName"));
    if(!is_object($pluginConfig)) throw new Exception("Plugins config $fileName is invalid");
    if(count($pluginConfig->plugins) > 0) foreach($pluginConfig->plugins as $plugin) {
      require(PFactory::getDir()."plugins/{$plugin->file}");
      $this->merchiumGoodsParser->addPlugin($plugin);
    }
    return $this;
  }


}

$goodsFiller = new GoodsFiller();
$goodsFiller->run();