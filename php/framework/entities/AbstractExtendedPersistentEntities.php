<?php
abstract class AbstractExtendedPersistentEntities extends AbstractExtendablePersistentEntities implements IExtendedEntities
{
  abstract protected function get_extension_fields_descriptor();
  private $fields_descriptor;
  public function get_fields_descriptor(){
    if(null === $this->fields_descriptor){
      $this->fields_descriptor = FieldsDescriptorFactory::get_instance()->get_merged(
        $this->get_base_family()->get_fields_descriptor(),
        $this->get_extension_fields_descriptor()
      );
    }
    return $this->fields_descriptor;
  }
}
?>
