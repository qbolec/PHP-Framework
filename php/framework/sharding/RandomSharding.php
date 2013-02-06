<?php
class RandomSharding extends SelectShardingWrapper implements ISharding
{
  protected function get_rng(){
    return Framework::get_instance()->get_rng();
  }
  public function get_shard_id_from_data_without_id($shards_count,array $data){
    return $this->get_rng()->next()%$shards_count;
  }
}
?>
