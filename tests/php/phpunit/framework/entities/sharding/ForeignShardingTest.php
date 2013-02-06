<?php
class ForeignShardingTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $select_sharding = $this->getMock('ISelectSharding');
    $sharding = new ForeignSharding('parent_id',$select_sharding);
    $this->assertInstanceOf('ISharding',$sharding);
  }
  public function testSelectGoById(){
    $select_sharding = $this->getMock('ISelectSharding');
    $select_sharding
      ->expects($this->once())
      ->method('get_shard_id_from_entity_id')
      ->with($this->equalTo(10),$this->equalTo(42))
      ->will($this->returnValue(13));
    $sharding = new ForeignSharding('parent_id',$select_sharding);
    $this->assertSame(13,$sharding->get_shard_id_from_entity_id(10,42));
  }
  public function testInsertsGoByForeignId(){
    $select_sharding = $this->getMock('ISelectSharding');
    $select_sharding
      ->expects($this->once())
      ->method('get_shard_id_from_entity_id')
      ->with($this->equalTo(10),$this->equalTo(42))
      ->will($this->returnValue(13));
    $sharding = new ForeignSharding('parent_id',$select_sharding);
    $this->assertSame(13,$sharding->get_shard_id_from_data_without_id(10,array('parent_id'=>42)));
  }
}
?>
