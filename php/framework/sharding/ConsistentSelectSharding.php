<?php
class ConsistentSelectSharding implements ISelectSharding
{
  private $ring_bits_count;
  private $assertions;
  const MACHINE_BITS = 64;
  public function __construct($ring_bits_count,IAssertions $assertions){
    $assertions->halt_unless(8<=$ring_bits_count && $ring_bits_count<self::MACHINE_BITS);//even though we can use MACHINE_BITS-bit integers, half of them are negative, so ring_bits_count must be smaller
    $this->ring_bits_count = $ring_bits_count;
    $this->assertions = $assertions;
  }
  public function get_shard_id_from_entity_id($shards_count,$id){
    $this->assertions->halt_unless(0==($shards_count&($shards_count-1)));//must be power of two
    $this->assertions->halt_unless(2<=$shards_count);//to avoid overflows (2^ring_bits_count might be negative) i divde both sides of division by 2
    $bucket_size =  ( 1 << ($this->ring_bits_count-1) ) / ($shards_count/2);
    return (int)($id / $bucket_size );
  }
}
?>
