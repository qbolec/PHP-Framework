<?php
class CacheLockFactory extends Singleton implements ILockFactory
{
  private function get_cache(){
    return Framework::get_instance()->get_cache_factory()->get_cache('locks');
  }
  public function get_lock($path){
    return new CacheLock($this->get_cache(),$path);
  }
}
?>
