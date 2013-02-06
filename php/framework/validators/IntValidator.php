<?php
class IntValidator extends AbstractValidator
{
  public function get_error($data){
    if(is_int($data)){ 
      return null;
    } else {  
      return new CouldNotConvertException($data);
    }
  }
}
?>
