<?php

/**
 * @package Plugins
 */

$plugin = function($html){
      
  if (preg_match('~<input\s+type="hidden"\s+name="objectId"\s+value="(.*)"~Ui', $html, $matches)) {
    $prefix = PApplication::getConfig()->shopPolicy->skuPrefixes->aliexpress;
    return $prefix.$matches[1];
  }
  return null;

};