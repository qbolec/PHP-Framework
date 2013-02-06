<?php
interface IRedisFactory extends IGetInstance
{
  public function get_redis($name);
}
?>
