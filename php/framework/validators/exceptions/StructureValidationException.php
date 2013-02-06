<?php
class StructureValidationException extends MultiValidationException
{
  public function to_tree(){
    $field_errors = array();
    foreach($this->validation_errors as $field_name => $validation_error){
      $field_errors[$field_name] = $validation_error->to_tree();
    }
    return array('fields'=>$field_errors);    
  }
}
?>
