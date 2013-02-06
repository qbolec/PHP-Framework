<?php
class MemcacheCacheFactory extends Singleton implements IConfigurableCacheFactory
{
  public function get_cache_from_config(ICacheFactory $factory,$config){
    $cluster = Arrays::grab($config,'cluster');
    $ttl = Arrays::grab($config,'ttl');
    $path = "memcaches/clusters/$cluster/servers";
    $servers = Config::get_instance()->get($path);
    return $this->spawn($servers,$ttl);
  }
  protected function spawn(array $servers,$ttl){
    return new MemcacheCache($servers,$ttl);
  }
}
?>
