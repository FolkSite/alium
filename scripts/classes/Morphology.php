<?php

class Morphology {

  private $morphy;
  private function __construct() {
    require_once(PFactory::getDir().'classes/extend/morphy/src/common.php');
    $dir = PFactory::getDir().'classes/extend/morphy/dicts';
    $lang = 'ru_RU';
    $dict_bundle = new phpMorphy_FilesBundle($dir, 'rus');
    $opts = array(
      // storage type, follow types supported
      // PHPMORPHY_STORAGE_FILE - use file operations(fread, fseek) for dictionary access, this is very slow...
      // PHPMORPHY_STORAGE_SHM - load dictionary in shared memory(using shmop php extension), this is preferred mode
      // PHPMORPHY_STORAGE_MEM - load dict to memory each time when phpMorphy intialized, this useful when shmop ext. not activated. Speed same as for PHPMORPHY_STORAGE_SHM type
      'storage' => PHPMORPHY_STORAGE_MEM,
      // Enable prediction by suffix
      'predict_by_suffix' => true, 
      // Enable prediction by prefix
      'predict_by_db' => true,
      // TODO: comment this
      'graminfo_as_text' => true,
      'use_ancodes_cache' => true
    );
    try {
    $this->morphy = new phpMorphy($dir, $lang, $opts);
    } catch(phpMorphy_Exception $e) {
        die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
    }
  }

  private static $instance;
  public function _() {
    if(isset(self::$instance)) return self::$instance;
    self::$instance = new self();
    return self::$instance;
  }

  public function __get($name) {
    return isset($this->$name) ? $this->$name : null;
  }

  private $text, $words, $nouns, $adjectives, $isProcessed = false;
  public function setText($text) {
    $this->text = $text;
    $string = preg_replace('~\s{2,}~', ' ', preg_replace('~[^А-Я ]~Uu', ' ', mb_convert_case($this->text, MB_CASE_UPPER, 'UTF-8')));
    $this->words = array_filter(array_unique(explode(' ', $string)));
    $this->isProcessed = false;
    return $this;
  }

  public function getNouns() {
    if(!$this->isProcessed) $this->process();
    return $this->nouns;
  }  

  public function getAdjectives() {
    if(!$this->isProcessed) $this->process();
    return $this->adjectives;
  }

  private function process() {
    $gramInfo = $this->morphy->getGramInfoMergeForms($this->words);
    $nouns = $adjectives = [];
    foreach($gramInfo as $word => $info) {
      if(!is_array($info) || count($info) == 0) continue;
      foreach($info as $variant) {
        if($variant['pos'] == 'С') {
          $nouns[] = $word;
          break;
        } elseif ($variant['pos'] == 'П') {
          $adjectives[] = $word;
          break;
        }
      }
    }
    $this->nouns = $this->arrayLemmatize($nouns);
    $this->adjectives = $this->arrayLemmatize($adjectives);
  }

  private static function arrayFirstElementLower(&$val, $key) {
    $val = mb_convert_case($val[0], MB_CASE_LOWER, 'UTF-8');
  }

  private function arrayLemmatize(array $words) {
    $words = $this->morphy->lemmatize($words);
    array_walk($words, 'self::arrayFirstElementLower');
    return array_unique(array_values($words));
  }



  /*
  $word = $m->morphy->castFormByGramInfo($word, null, array('ЕД', 'ПР'), true);
  */


}