<?php
abstract class ConfigurableRelation extends AbstractRelation implements IGetFieldsDescriptor
{
  public function __construct($config_name){
    parent::__construct(
      Framework::get_instance()
        ->get_relation_manager_factory()
        ->from_config_name_and_descriptor($config_name,$this->get_fields_descriptor())
    );
  }
}
?>
