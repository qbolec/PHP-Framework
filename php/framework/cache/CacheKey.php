<?php
class CacheKey implements ICacheKey
{
  private $cache;
  private $key_name;
  public function __construct(IPrefetchingCache $cache,$key_name){
    $this->cache = $cache;
    $this->key_name = $key_name;
  }
  private function get_cache(){
    return $this->cache;
  }
  public function get(){
    return $this->get_cache()->get($this->key_name);
  }
  public function set($value){
    return $this->get_cache()->set($this->key_name,$value);
  }
  public function add($value){
    return $this->get_cache()->add($this->key_name,$value);
  }
  public function increment($delta){
    return $this->get_cache()->increment($this->key_name,$delta);
  }
  public function delete(){
    return $this->get_cache()->delete($this->key_name);
  }
  public function increment_or_add($delta,$fallback_value){
    return $this->get_cache()->increment_or_add($this->key_name,$delta,$fallback_value);
  }
  public function prefetch(){
    return $this->get_cache()->prefetch($this->key_name);
  }
}
?>
