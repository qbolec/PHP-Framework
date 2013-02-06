<?php
//ta klasa nie jest mockowalna, 
//bo nie ma żadnych zależności i jest prosta jak budowa cepa
class Convert
{
  public static function to_int($whatever){
    if(is_int($whatever)){
      return $whatever;
    }else if(is_string($whatever)){
      $number = (int)$whatever;
      if((string)$number === $whatever){
        return $number;
      }
    }else if(is_float($whatever)){
      $number = (int)$whatever;
      if((float)$number === $whatever){
        return $number;
      }
    }else if(is_bool($whatever)){
      return $whatever?1:0;
    }
    throw new CouldNotConvertException($whatever);
  }
  public static function to_html($text){
    return htmlspecialchars($text,ENT_QUOTES,'UTF-8');
  }
}
?>
