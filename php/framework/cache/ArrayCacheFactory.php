<?php
class ArrayCacheFactory extends Singleton implements IConfigurableCacheFactory
{
  public function get_cache_from_config(ICacheFactory $factory,$config){
    return new ArrayCache();    
  }
}
?>
