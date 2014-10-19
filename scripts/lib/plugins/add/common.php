<?php

/**
 * @package Plugins
 */

$plugin = function($goods) {
  $config = PApplication::getConfig();
  $data =& $goods->data;
  $date = new DateTime('now', new DateTimeZone('Europe/Moscow'));
  // Product code ?
  $data['Language'] = 'ru';
  $data['Category'] = 'Тестовая категория';
  $data['List price'] = 0;
  $data['Status'] = 'A'; // Включен
  $data['Quantity'] = 100; // Количество товара на складе
  // $data['Weight'] = '';
  $data['Min quantity'] = 0;
  $data['Shipping freight'] = 0;
  $data['Date added'] = $date->format('d M Y H:i:s');
  $data['Downloadable'] = 'N';
  $data['Files'] = '';
  $data['Ship downloadable'] = 'N';
  $data['Inventory tracking'] = 'D';
  $data['Free shipping'] = 'Y';
  $data['Feature comparison'] = 'Y';
  $data['Zero price action'] = 'R';
  // $data['Thumbnail'] = '';
  // $data['Detailed image'] = '';
  $data['Taxes'] = 'VAT';
  // $data['Features'] = ''; // !!!
  // $data['Options'] = '';
  $data['Secondary categories'] = '';
  $data['Items in a box'] = 'min:0;max:0';
  $data['Box size'] = 'length:0;width:0;height:0';
  $data['Store'] = $config->name;

};