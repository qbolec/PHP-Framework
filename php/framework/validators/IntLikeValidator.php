<?php
class IntLikeValidator extends SimpleValidator
{
  public function normalize($data){
    if(is_int($data) || is_float($data) || is_string($data)){ 
      return Convert::to_int($data); 
    }else{
      throw new CouldNotConvertException($data);
    }
  }
}
?>
