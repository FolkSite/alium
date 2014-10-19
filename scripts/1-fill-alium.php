<?php

/**
 *  @package Alium
 */

require('lib/application.php');
PApplication::init();

/**
 *  Обновление базы товаров из выгрузки Мерчиума
 */

class GoodsFiller extends Cli {

  const MANUAL = 'Use {app} project.json file.csv';

  public function __construct() {
    PFactory::load('CSVGoods');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 3) die($this->showMan());
    
    $configFile = $argv[1];
    $dataFile = $argv[2];

    PApplication::loadConfig($configFile);
    
    $this->fill($dataFile);
  }
  
  private function fill($fileName) {

    $goodsFile = new CSVGoods($fileName);
    
    $i = 0;
    if(count($goodsFile) > 0) foreach ($goodsFile as $id => $goods) {
      $goods->save();
      $i++;
    }
    echo "Goods processed: $i".PHP_EOL;

  }


}

$goodsFiller = new GoodsFiller();
$goodsFiller->run();