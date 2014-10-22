<?php

/**
 * @package Plugins
 */

$plugin = function($html){
      
  if (preg_match('~window\.runParams\.imageBigViewURL=(\[.*\]);~Usi', $html, $matches)) {
    $data = json_decode($matches[1]);
    return $data;
  }
  return array();

};