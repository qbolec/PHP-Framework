<?php
abstract class AbstractEntity implements IEntity
{
  protected $data = array();
  public function get_id(){
    return $this->data['id'];
  }
  public function __construct(array $data){
    $family = $this->get_family();
    $validator = $family->get_fields_descriptor()->get_validator();
    $validator->must_match($data);
    $this->data = $data;
  }
  protected function get_field($field_name){
    return Arrays::grab($this->data,$field_name);
  }
}
?>
