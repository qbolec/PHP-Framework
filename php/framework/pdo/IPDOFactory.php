<?php
interface IPDOFactory{
  public function get_pdo($name, $shard_id);
  public function get_shards_count($name);
}
?>
