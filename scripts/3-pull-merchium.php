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

  const MANUAL = 'Use {app} project.json (option=[price])';

  public function __construct() {
  }

  public function run() {
    global $argv, $argc;
    if ($argc < 2) die($this->showMan());

    $configFile = $argv[1];
    $option = isset($argv[2]) ? $argv[2] : null;
    
    PApplication::loadConfig($configFile);

    $this->pull($option);
  }
  
  public function pull($option) {
    $config = PApplication::getConfig();
    $mongo = new MongoClient();
    $collection = $mongo->{$config->mongo->db}->{$config->mongo->collection};

    $logger = PFactory::getLogger();
    
    $cursor = $collection->find();

    $outputBuffer = fopen("php://output", 'w');

    $putHeader = true;
    $i = 0;
    $isPrice = $option == 'price';

    foreach ($cursor as $doc) {
      $i++;
      
      $goods = Goods::getInstance((object)$doc);

      if($putHeader) {
        $putHeader = false;
        fputcsv($outputBuffer, $isPrice ? $goods->getHeaderPrice() : $goods->getHeader());
      }
      
      fputcsv($outputBuffer, $isPrice ? $goods->getDataPrice() : $goods->getData());
    }

    fclose($outputBuffer);

    $logger->log(Code::INFO_ACTION_PULL, "Goods pulled: $i".($isPrice ? ' (price)' : null));
    
  }
}

$goodsPuller = new GoodsPuller();
$goodsPuller->run();