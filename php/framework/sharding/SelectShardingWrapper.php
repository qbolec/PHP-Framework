<?php
class SelectShardingWrapper implements ISelectSharding
{
  private $select_sharding;
  public function __construct(ISelectSharding $select_sharding){
    $this->select_sharding = $select_sharding;
  }
  public function get_shard_id_from_entity_id($shards_count,$id){
    return $this->select_sharding->get_shard_id_from_entity_id($shards_count,$id);
  }
}
?>
