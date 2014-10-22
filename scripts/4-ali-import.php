<?php

/**
 *  @package Alium
 */

require('core/application.php');
PApplication::init();

/**
 *  Импортер базы товаров в Алиум из интернет-магазина
 */

class AliImporter extends Cli {

  const MANUAL = 'Use {app} project.json uri';

  public function __construct() {
    PFactory::load('AliGoodsParser');
    PFactory::load('GoodsFromShop');
    PFactory::load('SeoModule');
    require_once(PFactory::getDir().'classes/extend/simple_html_dom/simple_html_dom.php');
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

    $config = PApplication::getConfig();

    $this->isPresent($uri) and die("Uri already present: $uri".PHP_EOL);

    $goods = new GoodsFromShop($uri);

    $aliGoodsParser = new AliGoodsParser();
    $aliGoodsParser->loadPluginsFromFile(PFactory::getDir().'plugins/ali.json', 'plugins');
    $aliGoodsParser->getContent($uri);

    $seoModule = new SeoModule();
    $seoModule->loadPluginsFromFile(PFactory::getDir().'plugins/seo.json', 'plugins');

    
    $goods->{'Product name'} = $aliGoodsParser->{'Product name'};
    $goods->{'Product code'} = $aliGoodsParser->{'Product code'};
    $goods->Price = $aliGoodsParser->Price;
    $goods->Weight = $aliGoodsParser->Weight;
    $images = $aliGoodsParser->{'Images'};
    $goods->{'Detailed image'} = $images[0];

    $seoModule->setText($aliGoodsParser->{'Product name'});
    $goods->{'Meta keywords'} = $seoModule->{'Meta keywords'};
    
    $goods->save();

    // echo $goods.PHP_EOL;
    

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