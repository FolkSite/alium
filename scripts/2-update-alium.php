<?php

require('lib/application.php');
PApplication::init();

class GoodsUpdater extends Cli {

  const MANUAL = 'Use {app} project.json';

  public function __construct() {
    PFactory::load('AliGoodsParser');
    PFactory::load('MerchiumGoods');
  }
  
  public function run() {
    global $argv, $argc;
    if ($argc < 2) die($this->showMan());

    $configFile = $argv[1];
    $this->loadConfig($configFile);
    
    $this->update();
  }
    
  private function update() {
    $mongo = new MongoClient();
    $collection = $mongo->{$this->config->mongo->db}->{$this->config->mongo->collection};
    
    $aliGoodsParser = new AliGoodsParser();
  
    $cursor = $collection->find();
    foreach ($cursor as $doc) {
      
      $merchiumGoods = new MerchiumGoods($this->config);
      $merchiumGoods->loadGood((object)$doc);
      
      $aliGoodsParser->getContent($doc['Origin goods url']);
      $data = $aliGoodsParser->parse();

      $merchiumGoods->updatePrice($data->Price);
      
      $merchiumGoods->save();
    }
    
  }

}

$goodsUpdater = new GoodsUpdater();
$goodsUpdater->run();