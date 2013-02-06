<?php
interface IInsertSharding
{
  public function get_shard_id_from_data_without_id($shards_count,array $data);
}
?>
