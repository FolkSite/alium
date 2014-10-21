<?php

/**
 *  @package Alium
 */

require('core/application.php');
PApplication::init();

/**
 *  Экспортет базы товаров в Мерчиум
 */

class GoodsPuller extends Cli {

  const MANUAL = 'Use {app} project.json';

  public function __construct() {
    PFactory::load('GoodsFromMongo');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 2) die($this->showMan());

    $configFile = $argv[1];
    
    PApplication::loadConfig($configFile);

    $this->pull();
  }
  
  public function pull() {
    $config = PApplication::getConfig();
    $mongo = new MongoClient();
    $collection = $mongo->{$config->mongo->db}->{$config->mongo->collection};
    
    $cursor = $collection->find();

    $outputBuffer = fopen("php://output", 'w');

    $putHeader = true;
    foreach ($cursor as $doc) {
      
      $goods = new GoodsFromMongo((object)$doc);

      if($putHeader) {
        $putHeader = false;
        fputcsv($outputBuffer, $goods->getHeader());
      }
      
      

      fputcsv($outputBuffer, $goods->getData());
    }

    fclose($outputBuffer);
    
  }
}

$goodsPuller = new GoodsPuller();
$goodsPuller->run();