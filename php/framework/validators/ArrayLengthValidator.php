<?php
class ArrayLengthValidator extends AbstractValidator
{
  private $length_validator;
  private $array_validator;
  public function __construct(IValidator $length_validator){
    $this->length_validator = $length_validator;
    $this->array_validator = new MapValidator(new AnythingValidator(),new AnythingValidator());
  }
  public function get_error($data){
    $array_error = $this->array_validator->get_error($data);
    if(null!==$array_error){
      return $array_error;
    }
    $length = count($data);
    return $this->length_validator->get_error($length);
  }
}
?>
