<?php
class FixedKeysValidator extends MapValidator
{
  private $field_validators;
  public function __construct(array $field_validators){
    foreach($field_validators as $field_name => $validator){
      $this->halt_unless($validator instanceof IValidator);
    }
    $this->field_validators = $field_validators;
    parent::__construct($this->get_key_validator(),new AnythingValidator());
  }
  protected function get_key_validator(){
    return new AnythingValidator();
  }
  public function get_error($data){
    $error = parent::get_error($data);
    $errors = array();
    if(null!==$error){
      $errors[] = $error;
    }
    if(is_array($data)){
      $missing = array_keys(array_diff_key($this->field_validators,$data));
      foreach($missing as $field_name){
        $errors[] = new IsMissingException($field_name);
      }
      $unexpected = array_keys(array_diff_key($data,$this->field_validators));
      foreach($unexpected as $field_name){
        $errors[] = new UnexpectedMemberException($field_name); 
      }
      $fields_errors = array();
      foreach(array_intersect_key($this->field_validators,$data) as $field_name => $validator){
        $field_error = $validator->get_error($data[$field_name]);
        if(null!==$field_error){
          $fields_errors[$field_name] = $field_error;
        }
      }
      if(0<count($fields_errors)){
        $errors[] = new StructureValidationException($fields_errors);
      }
    }
    return $this->compact_errors($errors);
  }
}
?>
