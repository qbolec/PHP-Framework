<?php
class LayeredPersistenceManagerFactory extends MultiInstance implements IConfigurablePersistenceManagerFactory
{
  public function from_config_and_descriptor(IPersistenceManagerFactory $factory,array $config,IFieldsDescriptor $descriptor){
    $near = $factory->from_config_name_and_descriptor(Arrays::grab($config,'near'),$descriptor);
    $far = $factory->from_config_name_and_descriptor(Arrays::grab($config,'far'),$descriptor);
    return new LayeredPersistenceManager($near,$far);
  } 
}
?>
