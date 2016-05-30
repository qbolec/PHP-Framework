<?php
class FloatLikeValidator extends SimpleValidator
{
  public function normalize($data){
    if(is_int($data) || is_float($data) || is_string($data)){ 
      return Convert::to_float($data); 
    }else{
      throw new CouldNotConvertException($data);
    }
  }
}
?>
