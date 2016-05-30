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
  public static function as_set(IPDO $pdo, array $data){
    $quoted = array_map(function($el)use($pdo){return $pdo->quote($el);},$data);
    return '(' . implode(',',$quoted) . ')';
  }
  public static function create_temporary_table(IPDO $pdo,$table_name,array $columns,array $rows){
    $pdo->prepare('CREATE TEMPORARY TABLE `' . $table_name . '` (' .
      implode(',',Arrays::map_assoc(function($name,$param){return "`$name` $param";},$columns)) .
    ')')->execute();
    if(!empty($rows)){
      $pdo->prepare('INSERT INTO `' . $table_name . '` (`' .
      implode('`,`',array_keys($columns)) .
      '`) VALUES ' .
      implode(',',array_map(function($row)use($pdo){
        return SQLHelper::as_set($pdo,$row);
      },$rows)))->execute();
    }
    return $pdo;
  }
  public static function drop_temporary_table(IPDO $pdo, $table_name){
    $pdo->prepare('DROP TEMPORARY TABLE `' . $table_name .'`')->execute();
    return $pdo;
  }
}
?>
