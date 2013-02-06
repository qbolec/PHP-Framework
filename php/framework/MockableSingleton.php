<?php
class MockableSingleton extends Singleton implements ISetInstance{
  public static function set_instance($instance){
    return parent::set_instance($instance);
  }
}
?>
