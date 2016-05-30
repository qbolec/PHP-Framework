<?php
class NullValidator extends AbstractValidator
{
  public function get_error($data){
    if(is_null($data)){
      return null;
    } else {
      return new CouldNotConvertException($data);
    }
  }
}
?>
