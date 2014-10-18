<?php

class PriceCalculator {
  const MIN_RATION = 1.05;

  private $config, $closure;
  public function __construct(stdClass $config) {
    $this->config = $config->price;
    eval($this->config->closure);
    if(!isset($closure)) throw new Exception("Error calc price: invalid closure {$this->config->closure}");
    $this->closure = $closure;
  }

  public function calcMarginPrice($originPrice) {
    $rawPrice = call_user_func($this->closure, $originPrice);

    if(!$this->isValidPrice($originPrice, $rawPrice)) return $this->minMargin($originPrice);

    if(!$this->config->round) return $rawPrice;
    
    $roundPrice = $this->roundPrice($rawPrice);
    if(!$this->isValidPrice($originPrice, $roundPrice)) return $rawPrice;

    if(!$this->config->sweet) return $roundPrice;

    $sweetPrice = $this->sweetPrice($roundPrice);
    if(!$this->isValidPrice($originPrice, $sweetPrice)) return $roundPrice;
    
    return $sweetPrice;
  }

  private function isValidPrice($originPrice, $newPrice) {
    return $newPrice / $originPrice > self::MIN_RATION;
  }

  private function minMargin($price) {
    return round($price * self::MIN_RATION, 2);
  }

  private function sweetPrice($price) {
    return $price - 1;
  }

  private function roundPrice($price) {
    $precision = $this->getPrecision($price);
    $roundPrice = ceil($price/$precision) * $precision;
    return $roundPrice;
  }

  private static $roundRange = array(
    1 => 1,
    2 => 1,
    3 => 10,
    4 => 100,
    5 => 100,
    6 => 1000
  );

  private function getPrecision($price) {
    $digits = strlen((int)$price);
    return isset(self::$roundRange[$digits]) ? self::$roundRange[$digits] : 1;
  }


}