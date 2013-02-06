<?php
class Strings
{
  public static function is_prefix_of($short,$long){
    return substr($long,0,strlen($short)) === $short;
  }
  public static function to_html($str){
    return htmlspecialchars($str,ENT_QUOTES);
  }
  public static function to_uri($str){
    return urlencode($str);
  }
}
?>
