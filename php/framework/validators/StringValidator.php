<?php
class StringValidator extends SimpleValidator
{
  public function normalize($data){
    if(is_string($data) && $data===Convert::to_utf8($data)){
      return $data;
    }else{
      throw new CouldNotConvertException($data);
    }
  }
}
?>
