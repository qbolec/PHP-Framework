<?php
interface IShardingFactory
{
  public function get_none();
  public function from_config_name($config_name);
  public function get_foreign_modulo($field_name);
}
?>
