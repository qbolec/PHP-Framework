<?php
class StackTraceLogWriter extends InfoLogWriter
{
  public function describe(array $backtrace,$info){
    return JSON::encode($backtrace) . '>' . parent::describe($backtrace,$info) ;
  }
}
?>
