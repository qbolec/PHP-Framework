<?php
class RedisFactory extends AbstractConfigurableConnectionsFactory implements IRedisFactory
{
  public function get_redis($name){
    $path = "redises/$name";
    return $this->get_connection_for_config_path($path);
  }

  protected function get_factory_by_type($type){
    $type_to_factory = array(
      'redis'=>'RedisDBFactory',
      'redirect'=>'RedirectRedisFactory',
      'master-slave'=>'MasterSlaveRedisFactory',
    );
    $factory_name = Arrays::grab($type_to_factory,$type);
    return $factory_name::get_instance();
  }
  protected function spawn(array $info){
    $config = Arrays::grab($info,'config');
    $type = Arrays::grab($info,'type');
    return $this->get_factory_by_type($type)->get_redis_from_config($this,$config);
  }
}
?>
