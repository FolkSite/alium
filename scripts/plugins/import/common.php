<?php

/**
 * @package Plugins
 * @see http://help.merchium.ru/support/articles/1000086585
 */

$plugin = function($goods) {
  $config = PApplication::getConfig();
  $data =& $goods->data;
  $date = new DateTime('now', new DateTimeZone('Europe/Moscow'));
  $data['Language'] = 'ru';
  $data['Category'] = $config->shopPolicy->category;
  $data['List price'] = 0;
  $data['Status'] = 'A'; // Включен
  $data['Quantity'] = 100; // Количество товара на складе
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
  $data['Thumbnail'] = ''; // todo
  $data['Detailed image'] = ''; // todo
  $data['Taxes'] = $config->shopPolicy->taxes;
  $data['Features'] = ''; // todo
  $data['Options'] = ''; // todo
  $data['Secondary categories'] = '';
  $data['Items in a box'] = 'min:0;max:0';
  $data['Box size'] = 'length:0;width:0;height:0';
  $data['Store'] = $config->name;
  $data['Description'] = '';
  $data['Short description'] = '';
  $data['Meta keywords'] = '';
  $data['Search words'] = '';
  $data['Page title'] = '';
  $data['SEO name'] = '';
  $data['Product URL'] = '';
  $data['Image URL'] = '';
  
  $data['YM Brand'] = '';
  $data['YM Country of origin'] = '';
  $data['YM Allow retail store purchase'] = 'Y';
  $data['YM Allow booking and self delivery'] = 'Y';
  $data['YM Allow delivery'] = 'Y';
  $data['YM Allow local delivery cost'] = 0;
  $data['YM Export Yes'] = 'Y';
  $data['YM Basic bid'] = 0;
  $data['YM Card bid'] = 0;
  $data['YM Model'] = '';
  $data['YM Sales notes'] = '';
  $data['YM typePrefix'] = '';
  $data['YM Market category'] = '';

  $data['TM Brand'] = '';
  $data['TM Model'] = '';
  $data['TM typePrefix'] = '';
  $data['TM Allow local delivery cost'] = 0;
  $data['TM Allow delivery'] = 'Y';
  $data['TM Allow booking and self delivery'] = 'Y';
  $data['TM MCP'] = 0;
  $data['TM Export Yes'] = 'Y';
  $data['YM Manufacturer warranty'] = '';
  $data['YM Seller warranty'] = '';

};