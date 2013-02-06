<?php
interface IConfigurablePersistenceManagerFactory
{
  public function from_config_and_descriptor(IPersistenceManagerFactory $factory,array $config,IFieldsDescriptor $descriptor);
}
?>
