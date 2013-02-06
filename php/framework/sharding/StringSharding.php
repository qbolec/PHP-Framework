<?php
class StringSharding implements ISharding
{
  private $inner;
  public function __construct(ISharding $inner){
    $this->inner = $inner;
  }
  public function get_shard_id_from_entity_id($shards_count,$id){
    $pseudo_id = crc32($id) & 0x7FFFFFFF;
    return $this->inner->get_shard_id_from_entity_id($shards_count,$pseudo_id);
  }
  public function get_shard_id_from_data_without_id($shards_count,array $data){
    return $this->inner->get_shard_id_from_data_without_id($shards_count,$data);
  }
}
?>
