<?php
interface ISelectSharding
{
  public function get_shard_id_from_entity_id($shards_count,$id);
}
?>
