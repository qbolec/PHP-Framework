<?php
abstract class HardcodedRelation extends AbstractRelation implements IGetFieldsDescriptor, IGetInstance
{
  public function __construct(){
    parent::__construct(
      Framework::get_instance()->get_relation_manager_factory()->get_array(
        $this->get_fields_descriptor(),
        $this->get_data(),
        $this->get_unique_keys()
      )
    );
  }
  abstract protected function get_unique_keys();
  abstract protected function get_data();
  public static function get_instance(){
    return new static();
  }
}
?>
