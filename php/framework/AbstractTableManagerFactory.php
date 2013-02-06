<?php
abstract class AbstractTableManagerFactory extends Singleton
{
  private $creations = array();
  abstract protected function get_type_to_factory();
  protected function get_factory_by_type($type){
    $type_to_factory = $this->get_type_to_factory();
    $factory_name = Arrays::grab($type_to_factory,$type);
    return $factory_name::get_instance();
  }
  abstract protected function get_path($name);
  private $config;
  public function from_config_name_and_descriptor($name,IFieldsDescriptor $fields_descriptor){
    $path = $this->get_path($name);
    if(!array_key_exists($path,$this->creations)){
      if(null === $this->config){
        $this->config = Config::get_instance();
      }
      $info = $this->config->get($path);
      $type = $info['type'];
      $config = $info['config'];

      $factory = $this->get_factory_by_type($type);
      $creation = $factory->from_config_and_descriptor($this,$config,$fields_descriptor);
      $this->creations[$path] = $this->wrap_creation($creation);
    }
    return $this->creations[$path];
  }
  protected function wrap_creation($creation){
    return $creation;
  }
}
?>
