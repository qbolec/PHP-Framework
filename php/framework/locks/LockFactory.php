<?php
class LockFactory extends MultiInstance implements ILockFactory
{
  public function get_lock($path){
    return CacheLockFactory::get_instance()->get_lock($path);
  }
}
?>
