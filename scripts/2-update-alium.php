<?php

/**
 *  @package Alium
 */

require('core/application.php');
PApplication::init();

/**
 *  Обновление цен и остатков
 */

class GoodsUpdater extends Cli {

  const MANUAL = 'Use {app} project.json';

  public function __construct() {
    PFactory::load('AliGoodsParser');
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
    $aliGoodsParser->loadPluginsFromFile(PFactory::getDir().'plugins/ali.json', 'plugins');
  
    $cursor = $collection->find();
    $i = 0;
    foreach ($cursor as $doc) {
      
      try {
        if(!isset($doc['Origin goods url'])) throw new Exception("Origin goods url not set!");
        $aliGoodsParser->getContent($doc['Origin goods url']);
      } catch (Exception $e) {
        continue;
      }
      
      $goods = Goods::getInstance((object)$doc);
      $goods->updatePrice($aliGoodsParser->Price);
      $goods->setProp('Last update', new MongoDate());
      $goods->save();
      $i++;
    }
    
    echo "Goods updated: $i".PHP_EOL;
  }

}

$goodsUpdater = new GoodsUpdater();
$goodsUpdater->run();