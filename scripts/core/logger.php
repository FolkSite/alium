<?php 

/**
 *  @package Core
 */

/**
 *  Ведение логов
 */


class Logger {

  private $dbo, $pid;
  public function __construct($projectName) {
    $this->dbo = PFactory::getDbo();
    $this->getPid($projectName);
  }

  private function getPid($projectName) {
    $projectName = addslashes($projectName);
    $query = "SELECT `id` FROM `projects` WHERE `name` = '$projectName'";
    $this->pid = $this->dbo->SelectValue($query);
    if ($this->pid == 0) {
      $query = "INSERT INTO `projects` (`name`) VALUES('$projectName')";
      $this->dbo->Query($query);
      $this->pid = $this->dbo->SelectLastInsertId();
    }
  }

  public function log($code, $description) {
    $code = (int)$code;
    $description = addslashes($description);
    $query = "INSERT INTO `operations` (`project`, `code`, `date`, `description`) VALUES({$this->pid}, $code, NOW(), '$description')";
    $this->dbo->Query($query);
  }



}