<?php
class NoShardingTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $sharding = new NoSharding();
    $this->assertInstanceOf('ISharding',$sharding);
    $this->assertInternalType('int',$sharding->get_shard_id_from_entity_id(1,42));
    $this->assertInternalType('int',$sharding->get_shard_id_from_data_without_id(1,array()));
  }
  public function testAlwaysZero(){
    $sharding = new NoSharding();
    $this->assertInstanceOf('ISharding',$sharding);
    $this->assertSame(0,$sharding->get_shard_id_from_entity_id(1,42));
    $this->assertSame(0,$sharding->get_shard_id_from_entity_id(1,43));
    $this->assertSame(0,$sharding->get_shard_id_from_data_without_id(1,array()));
    $this->assertSame(0,$sharding->get_shard_id_from_data_without_id(1,array('person_id'=>42)));
  }
  public function testHaltsIfSharded(){
    $assertions = $this->getMock('Assertions',array('halt'));
    $assertions
      ->expects($this->exactly(2))
      ->method('halt');
    $sharding = $this->getMock('NoSharding',array('get_assertions'));
    $sharding
      ->expects($this->any())
      ->method('get_assertions')
      ->will($this->returnValue($assertions));
    $sharding->get_shard_id_from_entity_id(2,42);
    $sharding->get_shard_id_from_data_without_id(2,array());
  }
}
?>
