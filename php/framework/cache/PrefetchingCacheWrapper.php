<?php
class PrefetchingCacheWrapper extends CacheWrapper implements IPrefetchingCache
{
  private $queue = array();
  public function get($key_name){
    if(array_key_exists($key_name,$this->queue)){
      $values = $this->execute_download();
      return Arrays::grab($values,$key_name);
    }
    return parent::get($key_name);
  }
  public function multi_get(array $key_names){
    $hashed = array_flip($key_names);
    if(0!=count(array_intersect_key($hashed,$this->queue))){
      $this->queue = Arrays::merge($this->queue,$hashed);
      $values = $this->execute_download();
      return array_intersect_key($values,$hashed);
    }
    return parent::multi_get($key_names);
  }
  public function prefetch($key_name){
    $this->queue[$key_name]=true;
  }
  private function execute_download(){
    $res = parent::multi_get(array_keys($this->queue));
    $this->queue = array();
    return $res;
  }
}
?>
