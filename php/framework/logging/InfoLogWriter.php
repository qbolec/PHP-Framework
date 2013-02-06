<?php
class InfoLogWriter extends Singleton implements ILogWriter
{
  public function describe(array $backtrace,$info){
    return JSON::encode($info);
  }
}
?>
