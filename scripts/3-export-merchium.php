<?php

/**
 *  @package Alium
 */

require('lib/application.php');
PApplication::init();

/**
 *  Экспортет базы товаров в Мерчиум
 */

class GoodsExporter extends Cli {

  const MANUAL = 'Use {app} project.json';

  public function __construct() {
    PFactory::load('GoodsFromMongo');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 2) die($this->showMan());

    $configFile = $argv[1];
    
    PApplication::loadConfig($configFile);

    $this->export();
  }
  
  public function export() {
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

$goodsExporter = new GoodsExporter();
$goodsExporter->run();