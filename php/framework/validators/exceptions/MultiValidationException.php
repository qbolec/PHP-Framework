<?php
class MultiValidationException extends Exception implements IValidationException
{
  protected $validation_errors;
  public function __construct(array $validation_errors){
    $assertions = Framework::get_instance()->get_assertions();
    foreach($validation_errors as $validation_error){
      $assertions->halt_unless($validation_error instanceof IValidationException);
    }
    $this->validation_errors = $validation_errors;
    $msgs = array();
    foreach($validation_errors as $validation_error){
      $msgs[]=$validation_error->getMessage();
    }
    parent::__construct(implode(",\n",$msgs));
  }
  private function merge_recursively($a,$b){
    $errors = Arrays::concat(Arrays::get($b,'errors',array()),Arrays::get($a,'errors',array()));
    $a_fields = Arrays::get($a,'fields',array());
    $b_fields = Arrays::get($b,'fields',array());
    $fields = Arrays::merge($a_fields,$b_fields);
    foreach(array_intersect_key($a_fields,$b_fields) as $field_name => $a_field){
      $fields[$field_name] = $this->merge_recursively($a_field,$b_fields[$field_name]);
    }
    $info = array();
    if(0<count($errors)){
      $info['errors'] = $errors;
    }
    if(0<count($fields)){
      $info['fields'] = $fields;
    }
    return $info;
  }
  public function to_tree(){
    $merged_info = array();
    foreach($this->validation_errors as $validation_error){
      $debug_info = $validation_error->to_tree();
      $merged_info = $this->merge_recursively($merged_info,$debug_info);
    }
    return $merged_info;
  }
}
?>
