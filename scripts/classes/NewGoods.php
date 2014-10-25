<?php

/**
 *  @package Goods
 */

/**
 *  Товар по типу ActiveRecord с сохранением в MongoDB, созданный из переданного массива
 */

class NewGoods extends Goods {

  public function __construct(array $goodsData) {
    parent::__construct();
    $this->init();

    if(count($goodsData) > 0) {
      foreach($goodsData as $key => $value) {
        $this->$key = $value;
      }
      $this->extractData();
    }
  }

  private function extractData() {
    if(!$this->getProp('src')) { $this->setProp('src', 'Ali'); }
    if (!$this->getProp('Origin goods url') && preg_match('~Ссылка:\sT\[(.*)\]~Ui', $this->{'Features'}, $mathes)) {
      $this->setProp('Origin goods url', $mathes[1]);
    }
  }

  

  public function save() {
    if($this->getProp('id') == 0) $this->setProp('id', $this->getNewId());
    parent::save();
  }

  private function getNewId() {
    $collection = $this->getCollection();
    $data = $collection->find()->sort(array('id' => -1))->getNext();
    if(isset($data['id'])) {
      return (int)($data['id']+1);
    } else {
      return 1;
    }
  }

  /**
   * Инициация всех свойств по-умолчанию
   */

  private function init() {
    $config = $this->getConfig();
    $date = new DateTime('now', new DateTimeZone('Europe/Moscow'));

    // Поля, переопределяемые в дочерних классах
    $this->{'Product id'} = 0;
    $this->{'Product name'} = '';
    $this->{'Product code'} = '';
    $this->{'Price'} = 0;
    $this->{'Weight'} = 0;
    $this->{'Detailed image'} = '';
    $this->{'Meta keywords'} = '';

    // Кастомизируются
    $this->{'Language'} = 'ru';
    $this->{'Category'} = $config->shopPolicy->category;
    $this->{'Status'} = 'A'; // Включен
    $this->{'Quantity'} = 100; // Количество товара на складе
    $this->{'Date added'} = $date->format('d M Y H:i:s');
    $this->{'Free shipping'} = 'Y';
    $this->{'Taxes'} = $config->shopPolicy->taxes;
    $this->{'Features'} = ''; // todo
    $this->{'Options'} = ''; // todo
    $this->{'Store'} = $config->name;
    $this->{'Description'} = ''; // todo
    $this->{'Short description'} = ''; // todo
    $this->{'Meta description'} = ''; // todo
    $this->{'Search words'} = ''; // todo
    $this->{'Page title'} = ''; // todo
    $this->{'SEO name'} = ''; // todo

    // Прочие поля
    $this->{'List price'} = 0;
    $this->{'Min quantity'} = 0;
    $this->{'Max quantity'} = 0;
    $this->{'Quantity step'} = 0;
    $this->{'List qty count'} = '';
    $this->{'Shipping freight'} = 0;
    $this->{'Downloadable'} = 'N';
    $this->{'Files'} = '';
    $this->{'Ship downloadable'} = 'N';
    $this->{'Inventory tracking'} = 'D';
    $this->{'Out of stock actions'} = '';
    $this->{'Feature comparison'} = 'Y';
    $this->{'Zero price action'} = 'R';
    $this->{'Thumbnail'} = '';
    $this->{'Secondary categories'} = '';
    $this->{'Items in a box'} = 'min:0;max:0';
    $this->{'Box size'} = 'length:0;width:0;height:0';
    $this->{'Product URL'} = '';
    $this->{'Image URL'} = '';
    $this->{'Detailed image URL'} = '';
    
    $this->{'YM Brand'} = '';
    $this->{'YM Country of origin'} = '';
    $this->{'YM Allow retail store purchase'} = 'Y';
    $this->{'YM Allow booking and self delivery'} = 'Y';
    $this->{'YM Allow delivery'} = 'Y';
    $this->{'YM Allow local delivery cost'} = 0;
    $this->{'YM Export Yes'} = 'Y';
    $this->{'YM Basic bid'} = 0;
    $this->{'YM Card bid'} = 0;
    $this->{'YM Model'} = '';
    $this->{'YM Sales notes'} = '';
    $this->{'YM typePrefix'} = '';
    $this->{'YM Market category'} = '';
    $this->{'YM Manufacturer warranty'} = '';
    $this->{'YM Seller warranty'} = '';

    $this->{'TM Brand'} = '';
    $this->{'TM Model'} = '';
    $this->{'TM typePrefix'} = '';
    $this->{'TM Allow local delivery cost'} = 0;
    $this->{'TM Allow delivery'} = 'Y';
    $this->{'TM Allow booking and self delivery'} = 'Y';
    $this->{'TM MCP'} = 0;
    $this->{'TM Export Yes'} = 'Y';
    
  }


}