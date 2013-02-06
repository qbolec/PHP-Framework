<?php
class RandomShardingTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $select_sharding = $this->getMock('ISelectSharding');
    $sharding = new RandomSharding($select_sharding);
    $this->assertInstanceOf('ISharding',$sharding);
    $this->assertSame(0,$sharding->get_shard_id_from_data_without_id(1,array()));
  }
  public function testSharding(){
    $randomness = $this->getMock('IRNG');
    $randomness
      ->expects($this->any())
      ->method('next')
      ->will($this->returnValue(4/*fair dice roll*/));

    $select_sharding = $this->getMock('ISelectSharding');
    $select_sharding
      ->expects($this->any())
      ->method('get_shard_id_from_entity_id')
      ->will($this->returnValue(9));
    $sharding = $this->getMock('RandomSharding',array('get_rng'),array($select_sharding));
    $sharding
      ->expects($this->any())
      ->method('get_rng')
      ->will($this->returnValue($randomness));
    $this->assertSame(9,$sharding->get_shard_id_from_entity_id(10,42));
    $this->assertSame(4,$sharding->get_shard_id_from_data_without_id(10,array()));
  
  }
}
?>
