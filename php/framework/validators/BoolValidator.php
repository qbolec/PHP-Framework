<?php
class BoolValidator extends AbstractValidator
{
  public function get_error($data){
    if(is_bool($data)){
      return null;
    } else {
      return new CouldNotConvertException($data);
    }
  }
}
?>
