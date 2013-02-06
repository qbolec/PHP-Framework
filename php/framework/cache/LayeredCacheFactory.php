<?php
class LayeredCacheFactory extends Singleton implements IConfigurableCacheFactory
{
  public function get_cache_from_config(ICacheFactory $factory,$config){
    $near = $factory->get_cache(Arrays::grab($config,'near'));
    $far = $factory->get_cache(Arrays::grab($config,'far'));
    return new LayeredCache($near,$far);  
  }
}
?>
