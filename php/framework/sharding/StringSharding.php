<?php
class StringSharding implements ISharding
{
  private $inner;
  public function __construct(ISharding $inner){
    $this->inner = $inner;
  }
  public function get_shard_id_from_entity_id($shards_count,$id){
    //we need a 63-bit integer (a 64-bit would be sometimes negative which will not work correctly with some $inner shardings)
    Framework::get_instance()->get_assertions()->halt_unless(is_string($id));
    $u = unpack('N2', sha1($id, true));
    $id64= ($u[1] << 32) | $u[2];
    $id63 = $id64 & 0x7FFFFFFFFFFFFFFF;
    return $this->inner->get_shard_id_from_entity_id($shards_count,$id63);
  }
  public function get_shard_id_from_data_without_id($shards_count,array $data){
    return $this->inner->get_shard_id_from_data_without_id($shards_count,$data);
  }
}
?>
