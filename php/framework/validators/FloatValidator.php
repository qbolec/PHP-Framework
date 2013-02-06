<?php
class FloatValidator extends AbstractValidator
{
  public function get_error($data){
    if(is_float($data)){
      return null;
    } else {
      return new CouldNotConvertException($data);
    }
  }
}
?>
