<?php
class CacheLock implements ILock
{
  private $cache_key;
  private $locked = false;
  public function __construct(IPrefetchingCache $cache,$path){
    $this->cache_key = new CacheKey($cache,'lock/'.$path);
    $this->lock();
  }
  private function lock(){
    $sleep_us = 10000;
    $give_up_us = 10000000;
    while(!$this->cache_key->add(1)){
      if($give_up_us<=0){
        throw new DeadlockException();
      }else{
        usleep($sleep_us);
        $give_up_us -= $sleep_us;
        $sleep_us*=2;
      }
    }
    $this->locked = true;
  }
  public function release(){
    $this->cache_key->delete();
    $this->locked = false;
  }
  public function __destruct(){
    if($this->locked){
      $this->release();
    }
  }
}
?>
