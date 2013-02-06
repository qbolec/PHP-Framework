<?php
class RedirectCacheFactory extends Singleton implements IConfigurableCacheFactory
{
  public function get_cache_from_config(ICacheFactory $factory,$config){
    return $factory->get_cache($config); 
  }
}
?>
