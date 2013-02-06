<?php
interface IFieldsDescriptorFactory{
  public function get_from_array(array $fields_description);
  public function get_merged(IFieldsDescriptor $a,IFieldsDescriptor $b);
}
?>
