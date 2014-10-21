<?php

/**
 * @package Plugins
 */

$plugin = function($html){
      
  if (preg_match('~itemprop="name">(.*)<\/~Usi', $html, $matches)) {
    return $matches[1];
  } else {
    throw new Exception("Can not parse name");
  }
};