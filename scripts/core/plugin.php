<?php

/**
 *  @package Сore
 */

/**
 *  Плагин
 */


trait Plugin {

  /**
   * Набор плагинов
   * @var $plugins array
   */
  protected $plugins = array();

  /**
   * Загрузка плагинов из файла
   * @param string $fileName имя файла
   * @param string путь к ветке плагинов
   */
  
  public function loadPluginsFromFile($fileName, $branch) {
    if(!file_exists($fileName)) throw new Exception("File $fileName not exists!");
    $pluginConfig = json_decode(file_get_contents($fileName));

    if(!is_object($pluginConfig)) throw new Exception("Plugins config $fileName is invalid");

    $allPlugins = $pluginConfig->{$branch};
    $dir = dirname($fileName);
    $this->loadPlugins($dir, $allPlugins);

    return $this;
  }

  /**
   * Загрузка плагинов из объекта
   * @param string $dir путь, в котором будут искаться плагины
   * @param object $allPlugins набор плагинов
   */

  public function loadPlugins($dir, $_allPlugins) {
    $allPlugins = (array)$_allPlugins;
    if(count($allPlugins) > 0) foreach($allPlugins as $section => $plugins) {
      if(count($plugins) > 0) foreach($plugins as $pluginObj) {
        require(PFactory::getDir()."plugins/{$pluginObj->file}");
        $this->addPlugin($section, $pluginObj->name, $plugin);
      }
    }
  }

  /**
   * Добавление одного плагина в секцию
   * @param string $section секция
   * @param callable $plugin замыкание
   */

  public function addPlugin($section, $pluginName, closure $plugin) {
    if (isset($this->plugins[$section][$pluginName])) throw new Exception("Duplicate plugin name `$pluginName` in section `$section`");
    $this->plugins[$section][$pluginName] = $plugin;
    return $this;
  }
}