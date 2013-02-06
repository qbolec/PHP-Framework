<?php
class SQLHelper
{
  public static function fields(array $data){
    return 0<count($data)?'`' . implode('`,`',array_keys($data)) . '`':'';
  }
  private static function field_placeholder_operations(array $data, $op){
    return array_map(
        function($name)use($op){
          return "`$name`$op:$name";
        },array_keys($data)
      );
  }
  public static function fields_match_placeholders(array $data, $strong_equality_operator){
    return implode(' AND ',self::field_placeholder_operations($data, $strong_equality_operator));
  }
  public static function assign_placeholders_to_fields(array $data){
    return implode(',',self::field_placeholder_operations($data,'='));
  }
  public static function placeholders(array $data){
    return 0<count($data)?':' . implode(',:',array_keys($data)):'';
  }
}
?>
