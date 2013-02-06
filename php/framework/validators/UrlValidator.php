<?php
class UrlValidator extends AbstractValidator 
{
  public function get_error($data){
    if(is_string($data)){
      if(filter_var($data,FILTER_VALIDATE_URL)){
        return null;
      }else{
        return new WrongValueException($data);
      }
    }else{
      return new CouldNotConvertException($data);
    }
  }
}
?>
