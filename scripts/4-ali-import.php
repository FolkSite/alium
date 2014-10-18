<?php

require('lib/application.php');
PApplication::init();

class AliImporter extends Cli {

  const MANUAL = 'Use {app} project.json uri';

  public function __construct() {
    PFactory::load('AliGoodsParser');
    PFactory::load('MerchiumGoods');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 2) die($this->showMan());

    $configFile = $argv[1];
    $this->loadConfig($configFile);

    $uri = $argv[2];

    $this->import($uri);
  }

  private function import($uri) {
    $this->isPrecent($uri) and die("Uri already present: $uri".PHP_EOL);

    $aliGoodsParser = new AliGoodsParser();
    
    

  }

  private function isPrecent($uri) {
    $mongo = new MongoClient();
    $collection = $mongo->{$this->config->mongo->db}->{$this->config->mongo->collection};
    $count = $collection->count(['Origin goods url'=> $uri]);
    return $count != 0;
  }




}

$aliImporter = new AliImporter();
$aliImporter->run();