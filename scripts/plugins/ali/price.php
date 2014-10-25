<?php

/**
 * @package Plugins
 */

$plugin = function($html){
      
  if (preg_match('~var skuProducts=(\[\{.*\}\]);~Usi', $html, $matches)) {
      $data = json_decode($matches[1]);
      $item = $data[0]; // TODO other items
      // Цена товара
      return $item->skuVal->skuMultiCurrencyCalPrice;
    } else {
      throw new Exception("Can not parse Price", Code::ERR_CAN_NOT_PARSE_PRICE);
    }
};