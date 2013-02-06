<?php
interface IRelationManagerFactory{
  public function from_config_name_and_descriptor($config_name,IFieldsDescriptor $fields_descriptor);
  public function get_array(IFieldsDescriptor $fields_descriptor,array $data,array $unique_keys);
}
?>
