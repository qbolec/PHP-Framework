<?php
class StringValidator extends SimpleValidator
{
  public function normalize($data){
    if(is_string($data)){
      return $data;
    }else{
      throw new CouldNotConvertException($data);
    }  
  }
}
?>
