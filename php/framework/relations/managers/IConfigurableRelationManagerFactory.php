<?php
interface IConfigurableRelationManagerFactory
{
  public function from_config_and_descriptor(IRelationManagerFactory $factory,array $config,IFieldsDescriptor $descriptor);
}
?>
