<?php
class ArrayCache implements ICache
{
  private $cached = array();
  const MAX_PREFERED_SIZE = 1000;
  public function get($key_name){
    return Arrays::grab($this->cached,$key_name);
  }
  public function multi_get(array $key_names){
    return Arrays::intersect_key($this->cached,array_flip($key_names));
  }
  public function set($key_name,$value){
    if(static::MAX_PREFERED_SIZE < count($this->cached)){
      array_shift($this->cached);
    }
    $this->cached[$key_name] = $value;
  }
  public function add($key_name,$value){
    if(array_key_exists($key_name,$this->cached)){
      return false;
    }else{
      $this->cached[$key_name] = $value;
      return true;
    }
  }
  public function increment($key_name,$delta){
    $this->cached[$key_name] = $delta + Arrays::grab($this->cached,$key_name);
    return $this->cached[$key_name];
  }
  public function delete($key_name){
    if(array_key_exists($key_name,$this->cached)){
      unset($this->cached[$key_name]);
      return true;
    }else{
      return false;
    }
  }
  public function increment_or_add($key_name,$delta,$fallback_value){
    if(array_key_exists($key_name,$this->cached)){
      $this->cached[$key_name]+=$delta;
    }else{
      $this->cached[$key_name]=$fallback_value;
    }
    return $this->cached[$key_name];
  }
}
?>
