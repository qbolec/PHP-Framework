<?php
class PDOSqlite extends PDOEx
{
  public function insert_ignore_command(){
    return 'INSERT OR IGNORE';
  }
  public function strong_equality_operator(){
    return ' IS ';
  }
}
?>
