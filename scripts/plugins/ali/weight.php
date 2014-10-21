<?php

/**
 * @package Plugins
 */

$plugin = function($html){
      
  if (preg_match('~pnl-packaging-weight"\s+rel="(.*)"~Usi', $html, $matches)) {
      return $matches[1];
    }
  return 0;
};