<?php
class AndValidator extends AbstractValidator
{
  private $validators;
  private $halt_on_first_error;
  public function __construct(array $validators, $halt_on_first_error = false){
    foreach($validators as $validator){
      $this->halt_unless($validator instanceof IValidator);
    }
    $this->validators = $validators;
    $this->halt_on_first_error = $halt_on_first_error;
  }
  public function get_error($data){
    $errors = array();
    foreach($this->validators as $validator){
      $error = $validator->get_error($data);
      if(null!==$error){
        $errors[]=$error;
        if($this->halt_on_first_error){
          break;
        }
      }
    }
    return $this->compact_errors($errors);
  }
}
?>
