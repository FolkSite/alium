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
    PFactory::load('GoodsFromShop');
    require_once(PFactory::getDir().'extend/simple_html_dom/simple_html_dom.php');
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 3) die($this->showMan());

    $configFile = $argv[1];
    
    PApplication::loadConfig($configFile);

    $uri = $argv[2];

    $this->import($uri);
  }

  private function import($uri) {

    $this->isPresent($uri) and die("Uri already present: $uri".PHP_EOL);

    $goods = new GoodsFromShop();

    $aliGoodsParser = new AliGoodsParser();
    $aliGoodsParser->getContent($uri);

    $goods->Price = $aliGoodsParser->price;
    $goods->{'Product name'} = $aliGoodsParser->name;
    // Description
    // Short description
    // Meta keywords
    // Meta keywords
    // Search words
    // Page title
    // SEO name

    /*
    echo "images: ".count($aliGoodsParser->images).PHP_EOL;
    */
    
    $goods->save();

    echo $goods.PHP_EOL;
    

  }

  private function isPresent($uri) {
    $mongo = new MongoClient();
    $config = PApplication::getConfig();
    $collection = $mongo->{$config->mongo->db}->{$config->mongo->collection};
    $count = $collection->count(['Origin goods url'=> $uri]);
    return $count != 0;
  }




}

$aliImporter = new AliImporter();
$aliImporter->run();