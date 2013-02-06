<?php
class FieldsDescriptorFactory extends MultiInstance implements IFieldsDescriptorFactory
{
  public function get_from_array(array $fields_description){
    return new FieldsDescriptor($fields_description);
  }
  public function get_merged(IFieldsDescriptor $a,IFieldsDescriptor $b){
    return new FieldsDescriptor(Arrays::merge(
      $a->get_description(),
      $b->get_description()
    ));
  }
}
?>
