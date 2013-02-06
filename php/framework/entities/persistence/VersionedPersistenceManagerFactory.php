<?php
class VersionedPersistenceManagerFactory extends MultiInstance implements IConfigurablePersistenceManagerFactory
{
  public function from_config_and_descriptor(IPersistenceManagerFactory $factory,array $config,IFieldsDescriptor $descriptor){
    $inner = $factory->from_config_name_and_descriptor(Arrays::grab($config,'inner'),$descriptor);
    $versioning_name = Arrays::grab($config,'versioning');
    $cvf = Framework::get_instance()->get_cache_versioning_factory();
    $versioning = $cvf->from_config_name($versioning_name);
    return new VersionedPersistenceManager($inner,$versioning);
  } 
}
?>
