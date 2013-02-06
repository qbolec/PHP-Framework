<?php
class SimplePDOPersistenceManagerFactory extends MultiInstance implements IConfigurablePersistenceManagerFactory
{
  public function from_config_and_descriptor(IPersistenceManagerFactory $factory,array $config,IFieldsDescriptor $descriptor){
    $sharding = Framework::get_instance()->get_sharding_factory()->from_config_name(Arrays::grab($config,'sharding')); 
    return new SimplePDOPersistenceManager($descriptor,$sharding,Arrays::grab($config,'pdo'),Arrays::grab($config,'table'));
  } 
}
?>
