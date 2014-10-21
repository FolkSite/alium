<?php

/**
 *  @package Alium
 */

require('lib/application.php');
PApplication::init();


class GoodMerchium extends Cli {
  const MANUAL = 'Use {app} project.json product.id';

  public function __construct() {
    PFactory::load('GoodsFromMongo');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 3) die($this->showMan());

    $configFile = $argv[1];
    
    PApplication::loadConfig($configFile);

    $productId = $argv[2];

    $this->show($productId);
  }

  private function show($productId) {
    $config = PApplication::getConfig();
    $mongo = new MongoClient();
    $collection = $mongo->{$config->mongo->db}->{$config->mongo->collection};
    
    $cursor = $collection->find(array('id' => $productId));
    
    foreach ($cursor as $doc) {
      echo var_dump($doc);
      break;
    }
  }


}

$goodMerchium = new GoodMerchium();
$goodMerchium->run();