<?php

/**
 *  @package Alium
 */

require('lib/application.php');
PApplication::init();

/**
 *  Обновление цен и остатков
 */

class GoodsUpdater extends Cli {

  const MANUAL = 'Use {app} project.json';

  public function __construct() {
    PFactory::load('AliGoodsParser');
    PFactory::load('GoodsFromMongo');
  }
  
  public function run() {
    global $argv, $argc;
    if ($argc < 2) die($this->showMan());

    $configFile = $argv[1];
    
    PApplication::loadConfig($configFile);
    
    $this->update();
  }

  private function update() {
    $config = PApplication::getConfig();
    $mongo = new MongoClient();
    $collection = $mongo->{$config->mongo->db}->{$config->mongo->collection};
    
    $aliGoodsParser = new AliGoodsParser();
  
    $cursor = $collection->find();
    $i = 0;
    foreach ($cursor as $doc) {
      
      $goods = new GoodsFromMongo((object)$doc);

      $aliGoodsParser->getContent($doc['Origin goods url']);

      $goods->updatePrice($aliGoodsParser->price);
      
      $goods->save();
      $i++;
    }
    
    echo "Goods updated: $i".PHP_EOL;
  }

}

$goodsUpdater = new GoodsUpdater();
$goodsUpdater->run();