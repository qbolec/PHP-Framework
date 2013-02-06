<?php
class MultiInstance implements IGetInstance{
  public static function get_instance(){
    return new static();
  }
}
?>
