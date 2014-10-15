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
    $dbmongo = $mongo->{$this->config->mongo->dbname};
    
    $aliGoodsParser = new AliGoodsParser();
  
    $cursor = $dbmongo->merchium->find();
    foreach ($cursor as $doc) {
      
      $merchiumGoods = new MerchiumGoods($this->config->mongo);
      $merchiumGoods->loadGood((object)$doc);
      
      $aliGoodsParser->getContent($doc['Origin goods url']);
      $data = $aliGoodsParser->parse();
      
      $merchiumGoods->updatePrice($data->Price);
      
      $merchiumGoods->save();
    }
    
  }
  
  // 1 http://ru.aliexpress.com/item/Apple-Shape-LED-Digital-Silicone-Band-Men-Women-Sports-Wristwatches-Casual-Outdoor-Watches-2014-New-Fashion/2030140316.html?s=p
  // 2 http://ru.aliexpress.com/item/2014-New-Men-s-High-Quality-Heavy-Stainless-Steel-Mens-Dress-Wrist-Watch/2023436381.html?s=p
  // 3 http://ru.aliexpress.com/item/Cooking-tools-Novelty-households-Bakeware-Kitchen-Vintage-Small-Dessert-Tea-Coffee-spoon-10pcs-lot-Free-shipping/1725477471.html?s=p
  // $aliGoodsParser->getContent(PFactory::getDir().'../../data/ali-html/1.html');
  // $aliGoodsParser->getContent('http://ru.aliexpress.com/item/4m-DC5V-WS2812B-led-pixel-srip-IP68-60pcs-WS2812B-M-T-1000S-controler-5V-60W-power/935640043.html?s=p');
  // $aliGoodsParser->parse();

}

$goodsUpdater = new GoodsUpdater();
$goodsUpdater->run();