<?php
class CacheWrapper implements ICache
{
  private $cache;
  public function __construct(ICache $cache){
    $this->cache = $cache;
  }
  public function get($key_name){
    return $this->cache->get($key_name);
  }
  public function multi_get(array $key_names){
    return $this->cache->multi_get($key_names);
  }
  public function set($key_name,$value){
    return $this->cache->set($key_name,$value);
  }
  public function add($key_name,$value){
    return $this->cache->add($key_name,$value);
  }
  public function increment($key_name,$delta){
    return $this->cache->increment($key_name,$delta);
  }
  public function delete($key_name){
    return $this->cache->delete($key_name);
  }
  public function increment_or_add($key_name,$delta,$fallback_value){
    return $this->cache->increment_or_add($key_name,$delta,$fallback_value);
  }
}
?>
