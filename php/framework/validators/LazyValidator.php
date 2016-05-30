<?php
class LazyValidator extends AbstractValidator
{
  private $get_validator;
  public function __construct($get_validator){
    $this->get_validator = $get_validator;
  }
  public function get_error($data){
    $foo=$this->get_validator;
    $foo()->get_error($data);
  }
}
?>
