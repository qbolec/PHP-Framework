<?php
class FixedValueValidator extends AbstractValidator
{
  private $expected;
  public function __construct($expected){
    $this->expected = $expected;
  }
  public function get_error($data){
    if($this->expected === $data){
      return null;
    } else {
      return new WrongValueException($data);
    }
  }
}
?>
