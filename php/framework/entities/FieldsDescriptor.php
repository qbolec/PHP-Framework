<?php
class FieldsDescriptor implements IFieldsDescriptor
{
  private $description;
  public function __construct(array $description){
    $this->description = $description;
  }
  public function get_description(){
    return $this->description;
  }
  private $validator;
  public function get_validator(){
    if(null === $this->validator){
      $this->validator = ValidatorFactory::get_instance()->get_persistence_data($this->get_description());
    }
    return $this->validator;
  }
}
?>
