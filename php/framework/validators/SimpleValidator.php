<?php
abstract class SimpleValidator extends AbstractValidator implements INormalizer
{
  public function get_error($data){
    try{
      $this->normalize($data);
      return null;
    }catch(CouldNotConvertException $e){
      return $e;
    }
  }
}
?>
