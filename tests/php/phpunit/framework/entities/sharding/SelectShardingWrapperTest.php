<?php
class SelectShardingWrapperTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $select_sharding = $this->getMock('ISelectSharding');
    $select_sharding
      ->expects($this->once())
      ->method('get_shard_id_from_entity_id')
      ->with($this->equalTo(10),$this->equalTo(42))
      ->will($this->returnValue(9));
    $wrapper = new SelectShardingWrapper($select_sharding);
    $this->assertInstanceOf('ISelectSharding',$wrapper);
    $this->assertSame(9,$wrapper->get_shard_id_from_entity_id(10,42));
  }
}
?>
