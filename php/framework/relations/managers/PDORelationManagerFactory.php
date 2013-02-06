<?php
class PDORelationManagerFactory extends MultiInstance implements IConfigurableRelationManagerFactory
{
  public function from_config_and_descriptor(IRelationManagerFactory $factory,array $config,IFieldsDescriptor $descriptor){
    $pdo_name = Arrays::grab($config,'pdo');
    $table_name = Arrays::grab($config,'table');
    $sharding = Framework::get_instance()->get_sharding_factory()->from_config_name(Arrays::grab($config,'sharding'));
    return new PDORelationManager($descriptor,$pdo_name,$table_name,$sharding); 
  }
}
?>
