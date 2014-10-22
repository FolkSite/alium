<?php

/**
 * @package Plugins
 */

$plugin = function($html){
      
  if (preg_match('~<input\s+type="hidden"\s+name="objectId"\s+value="(.*)"~Ui', $html, $matches)) {
    return 'ali-'.$matches[1];
  }
  return null;

};