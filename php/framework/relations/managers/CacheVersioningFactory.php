<?php
class CacheVersioningFactory extends AbstractConfigurableConnectionsFactory implements ICacheVersioningFactory
{
  public function from_config_name($name){
    $path = "versionings/$name";
    return $this->get_connection_for_config_path($path);
  }
  protected function spawn(array $info){
    $cache = Framework::get_instance()->get_cache_factory()->get_cache(Arrays::grab($info,'cache'));
    $key_prefix = Arrays::grab($info,'prefix');
    $columns = Arrays::grab($info,'columns');
    return new CacheVersioning($cache,$key_prefix,$columns);
  }
  public function from_cache_prefix_and_descriptor(IPrefetchingCache $cache,$prefix,IFieldsDescriptor $descriptor){
    $fields_list = array_keys($descriptor->get_description());
    return new CacheVersioning($cache,$prefix,$fields_list);
  }
}
?>
