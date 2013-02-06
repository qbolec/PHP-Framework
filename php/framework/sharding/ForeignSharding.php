<?php
class ForeignSharding extends SelectShardingWrapper implements ISharding
{
  private $foreign_key_name;
  public function __construct($foreign_key_name,ISelectSharding $select_sharding){
    parent::__construct($select_sharding);
    $this->foreign_key_name = $foreign_key_name;
  }

  public function get_shard_id_from_data_without_id($shards_count,array $data){
    $id = Arrays::grab($data,$this->foreign_key_name);
    return $this->get_shard_id_from_entity_id($shards_count,$id);
  }
}
?>
