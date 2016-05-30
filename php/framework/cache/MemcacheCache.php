<?php
class MemcacheCache implements ICache
{
  private $client;
  private $ttl;
  public function __construct(array $servers,$ttl){
    $this->halt_unless(0<count($servers));
    $this->halt_unless(0<=$ttl);
    $this->client = $this->get_client();
    $this->ttl = $ttl;
    $host_validator = new StringValidator();
    $port_validator = new IntValidator();
    foreach($servers as $endpoint){
      $this->halt_unless(is_array($endpoint));
      $host_validator->must_match(Arrays::grab($endpoint,'host'));
      $port_validator->must_match(Arrays::grab($endpoint,'port'));
    }
    foreach($servers as $endpoint){
      $this->client->addServer($endpoint['host'],$endpoint['port']);
    }
  }
  protected function halt_unless($b){
    if(!$b){
      Framework::get_instance()->get_assertions()->halt_unless($b);
    }
  }
  protected function get_client(){
    return new MemcacheEx();
  }
  private function serialize($value){
    $encoded = JSON::encode($value);
    $this->halt_unless(JSON::decode($encoded)===$value);//PRICELESS!
    return $encoded;
  }
  private function deserialize($value){
    //memcached server right-padds numbers with spaces during decrement
    return JSON::decode(rtrim($value));
  }
  public function get($key_name){
    $this->log($key_name);
    $value = $this->client->get($key_name);
    if(false === $value){
      throw new IsMissingException($key_name);
    }else{
      return $this->deserialize($value);
    }
  }
  public function multi_get(array $key_names){
    $this->log($key_names);
    $res = array();
    if(0==count($key_names)){
      return $res;
    }else{
      $values = $this->client->get($key_names);
      if(false!==$values){
        foreach($key_names as $key_name){
          if(array_key_exists($key_name,$values)){
            //client Memcache ma buga w deserializacji i czasem zwraca false
            if($values[$key_name]!==false){
              $res[$key_name] = $this->deserialize($values[$key_name]);
            }else{
              $this->log($key_name);
            }
          }
        }
      }else{
        $this->log();
      }
      return $res;
    }
  }
  public function set($key_name,$value){
    $this->log('set: '.$key_name);
    $serialized=$this->serialize($value);
    return $this->client->set($key_name,$serialized,0,$this->ttl);
  }
  public function add($key_name,$value){
    $this->log('add: '.$key_name);
    $serialized=$this->serialize($value);
    return $this->client->add($key_name,$serialized,0,$this->ttl);
  }
  public function increment($key_name,$delta){
    $this->log('inc: '.$key_name);
    if($delta<0){
      $value = $this->client->decrement($key_name,-$delta);
    }else{
      $value = $this->client->increment($key_name,$delta);
    }
    if(false === $value){
      throw new IsMissingException($key_name);
    }
    return $value;
  }
  public function delete($key_name){
    $this->log('del: '.$key_name);
    return $this->client->delete($key_name);
  }
  private $logger = null;
  private function log($info=null){
    if(null === $this->logger){
      $this->logger = Framework::get_instance()->get_logger();
    }
    return $this->logger->log($info);
  }
  public function increment_or_add($key_name,$delta,$fallback_value){
    try{
      return $this->increment($key_name,$delta);
    }catch(IsMissingException $e){
      if(false===$this->add($key_name,$fallback_value,$this->ttl)){
        return $this->increment($key_name,$delta);
      }else{
        return $fallback_value;
      }
    }
  }
  public function flush_all(){
    return $this->client->flush();
  }
}
?>
