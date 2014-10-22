<?php

/**
 * @package Plugins
 */

$plugin = function($html){
      
  if (preg_match('~var skuProducts=(\[\{.*\}\]);~Usi', $html, $matches)) {
    $data = json_decode($matches[1]);
    $item = $data[0]; // TODO other items
    // Цена при акции, может не быть
    return (isset($item->skuVal->actSkuMultiCurrencyCalPrice)) ? $item->skuVal->actSkuMultiCurrencyCalPrice : null;
  }
  return null;

};