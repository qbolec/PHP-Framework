<?php
class SimpleCachePersistenceManagerFactory extends MultiInstance implements IConfigurablePersistenceManagerFactory
{
  public function from_config_and_descriptor(IPersistenceManagerFactory $factory,array $config,IFieldsDescriptor $descriptor){
    $cache = Framework::get_instance()->get_cache_factory()->get_cache(Arrays::grab($config,'cache'));
    return new SimpleCachePersistenceManager($descriptor,$cache,Arrays::grab($config,'key_prefix'));
  } 
}
?>
