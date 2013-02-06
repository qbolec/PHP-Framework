<?php
class LayeredCache implements ICache
{
  protected $near;
  protected $far;
  public function __construct(ICache $near,ICache $far){
    $this->near = $near;
    $this->far = $far;
  }
  public function get($key_name){
    try{
      return $this->near->get($key_name);
    }catch(IsMissingException $e){
      $ret = $this->far->get($key_name);
      $this->near->set($key_name,$ret);
      return $ret;
    }
  }
  public function multi_get(array $key_names){
    $values = $this->near->multi_get($key_names);
    $misses = array_diff($key_names,array_keys($values));
    if(0==count($misses)){
      return $values;
    }else{
      $more_values = $this->far->multi_get($misses);
      foreach($more_values as $key => $value){
        $this->near->set($key,$value);//add?
      }
      return Arrays::merge($values,$more_values);
    }  
  }
  public function set($key_name,$value){
    $this->far->set($key_name,$value);
    $this->near->set($key_name,$value);
  }
  public function add($key_name,$value){
    if($this->far->add($key_name,$value)){
      $this->near->set($key_name,$value);
      return true;
    }else{
      $this->near->delete($key_name);
      return false;
    }
  }
  public function increment($key_name,$delta){
    try{
      $result = $this->far->increment($key_name,$delta);
      $this->near->set($key_name,$result);
      return $result;
    }catch(IsMissingException $e){
      $this->near->delete($key_name);
      throw $e;
    }
  }
  public function delete($key_name){
    $this->near->delete($key_name);
    return $this->far->delete($key_name);
  }
  public function increment_or_add($key_name,$delta,$fallback_value){
    try{
      $result = $this->far->increment_or_add($key_name,$delta,$fallback_value);
      $this->near->set($key_name,$result);
      return $result;
    }catch(IsMissingException $e){
      $this->near->delete($key_name);
      throw $e;
    }
  }
}
?>
