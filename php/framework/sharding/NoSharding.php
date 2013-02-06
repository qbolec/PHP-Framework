<?php
class NoSharding implements ISharding
{
  protected function get_assertions(){
    return Framework::get_instance()->get_assertions();
  }
  private function halt_if_sharded($shards_count){
    $this->get_assertions()->halt_unless(1 == $shards_count); 
  }
  public function get_shard_id_from_entity_id($shards_count,$id){
    $this->halt_if_sharded($shards_count);
    return 0;
  }
  public function get_shard_id_from_data_without_id($shards_count,array $data){
    $this->halt_if_sharded($shards_count);
    return 0;
  }
}
?>
