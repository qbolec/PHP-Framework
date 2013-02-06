<?php
class ModuloSelectShardingTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $sharding = new ModuloSelectSharding();
    $this->assertInstanceOf('ISelectSharding',$sharding);
    $this->assertInternalType('int',$sharding->get_shard_id_from_entity_id(1,42));
    $this->assertSame(4,$sharding->get_shard_id_from_entity_id(10,14));
  }
}
?>
