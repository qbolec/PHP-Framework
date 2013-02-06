<?php
class JSON
{
  public static function force_assoc(array $a){
    return (object)$a;
  }
  public static function encode($value){
    //one problem with json_encode
    //is that it encodes 1.0 as 1, not 1.0
    //@todo this should be done recursively!
    if(is_float($value) && (float)(int)$value===$value){
      $encoded = sprintf("%.1lf",$value);
    }else{
      //@todo: JSON_UNESCAPED_SLASHES once we get new PHP
      $encoded = json_encode($value);
    }
    return $encoded;
  }
  public static function decode($data){
    if(null === $data || ''==$data){
      throw new CouldNotConvertException($data);
    }
    $value = json_decode($data,true); 
    if(null === $value){
      $err = json_last_error();
      if(JSON_ERROR_NONE !== $err){
        throw new CouldNotConvertException($data);
      }
    }
    return $value;
  }
}
?>
