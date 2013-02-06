<?php
class MasterSlaveRedisFactory extends MultiInstance implements IConfigurableRedisFactory
{
  public function get_redis_from_config(IRedisFactory $factory, $config){
    $master = $factory->get_redis(Arrays::grab($config,'master'));
    $slave = $factory->get_redis(Arrays::grab($config,'slave'));
    return new MasterSlaveRedis($master,$slave);
  }
}
?>
