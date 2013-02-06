<?php
interface IConfigurableRedisFactory
{
  public function get_redis_from_config(IRedisFactory $factory, $config);
}
?>
