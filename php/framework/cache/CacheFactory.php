<?php
class CacheFactory extends AbstractConfigurableConnectionsFactory implements ICacheFactory
{
  public function get_cache($name){
    $path = "caches/$name";
    return $this->get_connection_for_config_path($path);
  }
  protected function get_factory_by_type($type){
    $type_to_factory = array(
      'memcache'=>'MemcacheCacheFactory',
      'layered'=>'LayeredCacheFactory',
      'redirect'=>'RedirectCacheFactory',
      'array'=>'ArrayCacheFactory',
    );
    $factory_name = Arrays::grab($type_to_factory,$type);
    return $factory_name::get_instance();
  }
  protected function spawn(array $info){
    $type = Arrays::grab($info,'type');
    $config = Arrays::grab($info,'config');
    return $this->get_factory_by_type($type)->get_cache_from_config($this,$config);
  }
}
?>
