<?php
class CachedFunction implements ICachedFunction{
  private $cache;
  private $key_name_prefix;
  private $foo;
  public function __construct(ICache $cache,$foo,$key_name_prefix=null){
    $this->cache = $cache;
    $this->foo = $foo;
    if($key_name_prefix===null){
      Framework::get_instance()->get_assertions()->halt_unless(is_array($foo) && count($foo)==2 && is_string($foo[1]) && (is_object($foo[0]) || is_string($foo[0])));
      $class_name = is_string($foo[0]) ? $foo[0] : get_class($foo[0]);
      $foo_name = $foo[1];
      $key_name_prefix = $class_name . '::' . $foo_name;
    }
    $this->key_name_prefix = $key_name_prefix;
  }
  private function get_cache_key(/*args*/){
    $args = func_get_args();
    return new CacheKey($this->cache,$this->key_name_prefix . '(' . implode(',',array_map(array('JSON','encode'),$args)) . ')' );
  }
  public function delete(/*args*/){
    $args = func_get_args();
    $cache_key = call_user_func_array(array($this,'get_cache_key'), $args);
    $cache_key->delete();
  }
  public function get(/*args*/){
    $args = func_get_args();
    $cache_key = call_user_func_array(array($this,'get_cache_key'), $args);
    try{
      return $cache_key->get();
    }catch(IsMissingException $e){
      $result = call_user_func_array($this->foo,$args);
      $cache_key->set($result);
      return $result;
    }
  }
}

