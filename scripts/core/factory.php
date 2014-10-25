<?php

/**
 *  @package Сore
 */
 
/**
 *  Фабрика объектов
 */

abstract class PFactory {

  /**
   * Load additional classes
   *
   */

  public function load($_baseClassName)
  {
    if(is_array($_baseClassName) && count($_baseClassName) == 0) return false;
    if(empty($_baseClassName)) return false;
    $baseClassNames = (array)$_baseClassName;
    foreach($baseClassNames as $baseClassName)
    {
      require_once(self::$dir."classes/$baseClassName.php");
    }
    return true;
  }

  /**
   * Init common classes
   *
   */
   
  private static $dir;
  public static function init()
  {
    self::$dir = realpath(__DIR__.'/..').'/';
    require_once(self::$dir.'core/plugin.php');
    require_once(self::$dir.'core/mysql.php');
    require_once(self::$dir.'core/cli.php');
    require_once(self::$dir.'core/parser.php');
    require_once(self::$dir.'core/goods.php');
    require_once(self::$dir.'core/logger.php');
    require_once(self::$dir.'core/code.php');
  }
  
  /**
	 * Get library directory.
	 *
	 */
  
  public static function getDir()
  {
    return self::$dir;
  }

  /**
	 * Get a database object.
	 *
	 */

  private static $database = null;
	public static function getDbo()
	{
		if (!self::$database)
		{
			self::$database = new DataBaseMysql(PApplication::getConfig()->mysql);
		}

		return self::$database;
	}

  private static $logger;
  public function getLogger() {
    if(!isset(self::$logger)) {
      self::$logger = new Logger(PApplication::getConfig()->name);
    }
    return self::$logger;
  }
  
  /**
   * Set global option
   *
   */  
  
  private static $options = array();
  public static function setOpt($name, $value)
  {
    self::$options[$name] = $value;
  }
  
  /**
   * Get global option
   *
   */
  
  public static function getOpt($name)
  {
    return isset(self::$options[$name]) ? self::$options[$name] : null;
  } 
  
}