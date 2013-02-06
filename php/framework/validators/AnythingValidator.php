<?php
class AnythingValidator extends AbstractValidator
{
  public function get_error($data){
    return null;
  }
  //optimization
  public function is_valid($data){
    return true;
  }
  //optimization
  public function must_match($data){
  }
}
?>
