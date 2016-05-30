<?php
//ta klasa nie jest mockowalna, 
//bo nie ma żadnych zależności i jest prosta jak budowa cepa
class Convert
{
  public static function to_float($whatever){
    if(is_int($whatever)){
      return (float)$whatever;
    }else if(is_string($whatever)){
      $number = (float)$whatever;
      return $number;//no bo jak inaczej?
    }else if(is_float($whatever)){
      return $whatever;
    }else if(is_bool($whatever)){
      return $whatever?1.0:0.0;
    }
    throw new CouldNotConvertException($whatever);
  }
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
  public static function to_utf8($text){
    ini_set('mbstring.substitute_character', "none"); 
    return mb_convert_encoding($text, 'UTF-8', 'UTF-8'); 
  }
}
?>
