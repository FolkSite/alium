<?php

/**
 *  @package Alium
 */

require('lib/application.php');
PApplication::init();

/**
 *  Импортер базы товаров в Алиум из интернет-магазина
 */

class AliImporter extends Cli {

  const MANUAL = 'Use {app} project.json uri';

  public function __construct() {
    PFactory::load('AliGoodsParser');
    PFactory::load('MerchiumGoods');
    require_once(PFactory::getDir().'extend/simple_html_dom/simple_html_dom.php');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 2) die($this->showMan());

    $configFile = $argv[1];
    
    PApplication::loadConfig($configFile);

    $uri = $argv[2];

    $this->import($uri);
  }

  private function import($uri) {
    $this->isPrecent($uri) and die("Uri already present: $uri".PHP_EOL);

    $aliGoodsParser = new AliGoodsParser();
    $aliGoodsParser->getContent($uri);

    echo $aliGoodsParser->images.PHP_EOL;
    echo $aliGoodsParser->price.PHP_EOL;
    echo $aliGoodsParser->name.PHP_EOL;
    
    

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