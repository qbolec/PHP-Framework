<?php
class ConsistentSelectShardingTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $assertions = $this->getMock('IAssertions');
    $sharding = new ConsistentSelectSharding(32,$assertions);
    $this->assertInstanceOf('ISelectSharding',$sharding);
    $this->assertInternalType('int',$sharding->get_shard_id_from_entity_id(16,42));
  }
  /**
   * @dataProvider good
   */
  public function testWorks($bits,$shards_count,$id,$shard_id){
    $assertions = $this->getMock('Assertions',array('warn','halt'));
    $assertions
      ->expects($this->never())
      ->method('warn');
    $assertions
      ->expects($this->never())
      ->method('halt');
    $sharding = new ConsistentSelectSharding($bits,$assertions);
    $this->assertSame($shard_id,$sharding->get_shard_id_from_entity_id($shards_count,$id));
  }
  public function good(){
    return array(
      array(8,256,17,17),
      array(8,16,17,1),
      array(8,16,16,1),
      array(31,2,1<<30,1),
      array(31,16,1<<30,8),
      array(31,16,(1<<30)-1,7),
      //@todo: when we move to 64-bit machine, add these tests:
      //array(32,16,16,0),
      //array(32,16,1234,0),
      //array(32,16,1<<31,8),
      //array(32,16,(1<<32)-1,15),
      //array(63,16,(1<<63)-1,15),
      //array(63,16,(1<<62),8),
      //array(63,16,(1<<62)-1,7),
    );
  }
}
?>
