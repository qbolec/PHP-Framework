<?php
class SingletonFlusher extends Singleton
{
  public function flush(){
    parent::$instances = array();
  }
}
?>
