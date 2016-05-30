<?php
class ForeignSharding extends SelectShardingWrapper implements ISharding
{
  private $foreign_key_name;
  private $insert_sharding;
  public function __construct($foreign_key_name,ISelectSharding $select_sharding,ISelectSharding $insert_sharding=null){
    parent::__construct($select_sharding);
    if($insert_sharding===null){
      $insert_sharding = $select_sharding;
    }
    $this->insert_sharding = $insert_sharding;
    $this->foreign_key_name = $foreign_key_name;
  }

  public function get_shard_id_from_data_without_id($shards_count,array $data){
    $id = Arrays::grab($data,$this->foreign_key_name)?:0;
    return $this->insert_sharding->get_shard_id_from_entity_id($shards_count,$id);
  }
}
?>
