<?php
class LineLogWriter extends InfoLogWriter
{
  public function describe(array $backtrace,$info){
    $current = $backtrace[0];
    $file = Arrays::get($current,'file','definition of ' . Arrays::get($current,'class','unknown-class'));
    $line = Arrays::get($current,'line','definition of ' . Arrays::get($current,'function','unknown-function'));
    return $file . '@' . $line . '>' . parent::describe($backtrace,$info);
  }
}
?>
