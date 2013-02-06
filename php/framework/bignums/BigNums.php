<?php
class BigNums
{
  public static function shl($float_num, $shift){
    return $float_num * pow(2,$shift);
  }
  public static function shr($float_num, $shift){
    if(0<=$float_num){
      return floor($float_num / pow(2,$shift));
    }else{
      return ceil($float_num / pow(2,$shift));
    }
  }
  public static function lsbits($float_num, $bits_count){
    return (int)fmod($float_num,pow(2,$bits_count));
  }
}
?>
