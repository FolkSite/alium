<?php

/**
 * @package Plugins
 */

$plugin = function($text) {

  return implode(', ', $this->getMorphology()->getNouns());

};