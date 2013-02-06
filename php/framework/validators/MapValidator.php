<?php
class MapValidator extends AbstractValidator
{
  private $key_validator;
  private $value_validator;
  public function __construct(IValidator $key_validator,IValidator $value_validator){
    $this->key_validator = $key_validator;
    $this->value_validator = $value_validator;
  }
  public function get_error($data){
    if(!is_array($data)){
      return new CouldNotConvertException($data);
    }
    $fields_errors = array();
    foreach($data as $key => $value){
      $key_error = $this->key_validator->get_error($key);
      if(null!==$key_error){
        $fields_errors[$key][] = $key_error;
      }
      $value_error = $this->value_validator->get_error($value);
      if(null!==$value_error){
        $fields_errors[$key][] = $value_error;
      }
    }
    if(0<count($fields_errors)){
      $structure_errors = array();
      foreach($fields_errors as $key=>$errors){
        if(1<count($errors)){
          $structure_errors[$key] = new MultiValidationException($errors);
        }else{
          $structure_errors[$key] = $errors[0];
        }
      }
      return new StructureValidationException($structure_errors);
    }else{
      return null;
    }
  }
}
?>
