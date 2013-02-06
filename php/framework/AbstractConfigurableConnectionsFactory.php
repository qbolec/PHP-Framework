<?php
abstract class AbstractConfigurableConnectionsFactory extends Singleton
{
  protected $connections = array();
  protected function get_connection_for_config_path($path){
    $key = $path;
    if(!array_key_exists($key,$this->connections)){
      $info = $this->get_config()->get($path);
      $this->connections[$key] = $this->spawn($info);
    }
    return $this->connections[$key];
  }
  private $config;
  protected function get_config(){
    if(null === $this->config){
      $this->config = Config::get_instance();
    }
    return $this->config;
  }
  abstract protected function spawn(array $info);
}
?>
