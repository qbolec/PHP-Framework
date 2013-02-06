<?php
class OrValidator extends AbstractValidator
{
  private $validators;
  public function __construct(array $validators){
    $this->halt_unless(0<count($validators));//sorry, but empty alternative is not currently supported
    foreach($validators as $validator){
      $this->halt_unless($validator instanceof IValidator);
    }
    $this->validators = $validators;
  }
  public function get_error($data){
    $errors = array();
    foreach($this->validators as $validator){
      $error = $validator->get_error($data);
      if(null===$error){
        return null;
      }
      $errors[]=$error;
    }
    return $this->compact_errors($errors);
  }
}
?>
