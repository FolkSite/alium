<?php 

/**
 *  @package Сore
 */

/**
 *  Парсер
 */

abstract class Parser {

  /**
   * Набор плагинов
   * @var $plugins array
   */
  protected $plugins = array();

  /**
   * Загрузка плагинов из файла
   * @param string $fileName имя файла
   */
  
  public function loadPlugins($fileName) {
    $pluginConfig = json_decode(file_get_contents($fileName));
    if(!is_object($pluginConfig)) throw new Exception("Plugins config $fileName is invalid");
    $dir = dirname($fileName);
    $allPlugins = (array)$pluginConfig->plugins;

    if(count($allPlugins) > 0) foreach($allPlugins as $section => $plugins) {
      if(count($plugins) > 0) foreach($plugins as $plugin) {
        require(PFactory::getDir()."plugins/{$plugin->file}");
        $this->addPlugin($section, $plugin);
      }
    }
    return $this;
  }

  /**
   * Добавление одного плагина в секцию
   * @param string $section секция
   * @param callable $plugin замыкание
   */

  public function addPlugin($section, closure $plugin) {
    $this->plugins[$section][] = $plugin;
    return $this;
  }



}