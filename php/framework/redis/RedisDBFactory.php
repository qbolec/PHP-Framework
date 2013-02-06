<?php
class RedisDBFactory extends MultiInstance implements IConfigurableRedisFactory
{
  public function get_redis_from_config(IRedisFactory $factory, $config){
    $host = Arrays::grab($config,'host');
    $port = Arrays::grab($config,'port');
    $ttl = Arrays::grab($config,'ttl');
    $persistent = Arrays::grab($config,'persistent');
    $password = Arrays::get($config,'password');
    return new RedisDB(new RedisEx(),$host,$port,$ttl,$persistent,$password); 
  }
}
?>
