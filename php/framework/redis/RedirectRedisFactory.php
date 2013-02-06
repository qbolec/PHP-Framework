<?php
class RedirectRedisFactory extends MultiInstance implements IConfigurableRedisFactory
{
  public function get_redis_from_config(IRedisFactory $factory, $config){
    return $factory->get_redis($config);
  }
}
?>
