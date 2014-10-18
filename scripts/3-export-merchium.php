<?php

require('lib/application.php');
PApplication::init();

class GoodsExporter extends Cli {

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

    $this->export();
  }
  
  public function export() {
    
    $mongo = new MongoClient();
    $collection = $mongo->{$this->config->mongo->db}->{$this->config->mongo->collection};
    
    $cursor = $collection->find();

    $outputBuffer = fopen("php://output", 'w');

    $putHeader = true;
    foreach ($cursor as $doc) {
      
      $merchiumGoods = new MerchiumGoods($this->config);
      $merchiumGoods->loadGood((object)$doc);

      if($putHeader) {
        $putHeader = false;
        fputcsv($outputBuffer, $merchiumGoods->getHeader());
      }
      
      

      fputcsv($outputBuffer, $merchiumGoods->getData());
    }

    fclose($outputBuffer);
    
  }
}

$goodsExporter = new GoodsExporter();
$goodsExporter->run();