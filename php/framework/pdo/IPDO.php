<?php
interface IPDO //unfortunatelly native PDO does not have any interface, which makes mocking harder
{
  public function prepare($sql);
  public function lastInsertId();
  public function insert_ignore_command();
  public function strong_equality_operator();
}
?>
