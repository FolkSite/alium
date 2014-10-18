<?php

require(__DIR__.'/../scripts/lib/application.php');

PApplication::init();

class PriceTester {

  public function __construct() {
    PFactory::load('PriceCalculator');
  }

  private $minRatio = 1000, $minMarginCase = 'No yep', $maxRatio = 0, $maxMarginCase = 'No yep';
  private function testRatio($ratio, $margin, $price, $newPrice) {
    if($ratio < $this->minRatio) {
      $this->minRatio = $ratio;
      $this->minMarginCase = "Ratio: $ratio, Margin: $margin, price: $price, newPrice: $newPrice";
    }
    if($ratio > $this->maxRatio) {
      $this->maxRatio = $ratio;
      $this->maxMarginCase = "Ratio: $ratio, Margin: $margin, price: $price, newPrice: $newPrice";
    }
  }

  
  public function run() {
    $config = new StdClass();
    $config->price = new StdClass();
    
    $boolCases = array(true, false);
    $i = 0;

    foreach($boolCases as $case1) {
      foreach($boolCases as $case2) {
        $config->price->round = $case1;
        $config->price->sweet = $case2;

        for($margin = 0.95; $margin <= 1.35; $margin += 0.05) {
        
          $config->price->closure = '$closure = function($price) { return $price * '.$margin.'; };';
          $this->priceCalculator = new PriceCalculator($config);
          
          for($price = 0.15; $price <= 20; $price += 0.05) {
            $newPrice = $this->priceCalculator->calcMarginPrice($price);
            $ratio = $newPrice / $price;
            if($ratio < 1.04) throw new Exception("Margin: $margin, price: $price, newPrice: $newPrice");
            $this->testRatio($ratio, $margin, $price, $newPrice);
            $i++;
          }

          for($price = 20; $price <= 2000; $price += 25) {
            $newPrice = $this->priceCalculator->calcMarginPrice($price);
            $ratio = $newPrice / $price;
            if($ratio < 1.04) throw new Exception("Margin: $margin, price: $price, newPrice: $newPrice");
            $this->testRatio($ratio, $margin, $price, $newPrice);
            $i++;
          }

          for($price = 2000; $price <= 20000; $price += 75) {
            $newPrice = $this->priceCalculator->calcMarginPrice($price);
            $ratio = $newPrice / $price;
            if($ratio < 1.04) throw new Exception("Margin: $margin, price: $price, newPrice: $newPrice");
            $this->testRatio($ratio, $margin, $price, $newPrice);
            $i++;
          }

          for($price = 20000; $price <= 200000; $price += 750) {
            $newPrice = $this->priceCalculator->calcMarginPrice($price);
            $ratio = $newPrice / $price;
            if($ratio < 1.04) throw new Exception("Margin: $margin, price: $price, newPrice: $newPrice");
            $this->testRatio($ratio, $margin, $price, $newPrice);
            $i++;
          }
          
        }
      }
    }

  echo "Tested $i cases".PHP_EOL;
  echo "Min ratio: {$this->minMarginCase}".PHP_EOL;
  echo "Max ratio: {$this->maxMarginCase}".PHP_EOL;
    

    

  }

}

$priceTester = new PriceTester();
$priceTester->run();