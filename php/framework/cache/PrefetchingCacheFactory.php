<?php
class PrefetchingCacheFactory extends Singleton implements IPrefetchingCacheFactory
{
  private $caches = array();
  protected function get_cache_factory(){
    return CacheFactory::get_instance();
  }
  public function get_cache($name){
    if(!array_key_exists($name,$this->caches)){
      $this->caches[$name] = new PrefetchingCacheWrapper($this->get_cache_factory()->get_cache($name));
    }
    return $this->caches[$name];
  }
}
?>
