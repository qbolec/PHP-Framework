<?php
class MasterSlaveRedisTest extends FrameworkTestCase
{
  public function getSUT($master,$slave){
    return new MasterSlaveRedis($master,$slave);
  }
  public function testInterface(){
    $master = $this->getMock('IRedisDB');
    $slave = $this->getMock('IRedisDB');
    $master_slave = $this->getSUT($master,$slave);
    $this->assertInstanceOf('IRedisDB',$master_slave);
  }
  /**
   * @dataProvider getForwardingSpecification
   */
  public function testForwarding($foo_name,$modifies,$args,$return_value){
    $master = $this->getMock('IRedisDB');
    $slave = $this->getMock('IRedisDB');
    $master_slave = $this->getSUT($master,$slave);
    if($modifies){
      $operator= $master;
      $sleep = $slave;
    }else{
      $operator = $slave;
      $sleep = $master;
    }
    
    $part1 = $operator
      ->expects($this->once())
      ->method($foo_name);
    $part2 = call_user_func_array(array($part1,'with'),$args);
    $part2
      ->will($this->returnValue($return_value));

    $sleep
      ->expects($this->never())
      ->method($foo_name);

    $result = call_user_func_array(array($master_slave,$foo_name),$args);
    $this->assertSame($return_value,$result);
  }
  public function getForwardingSpecification(){
    return array(
      array('z_card',false,array('key'),7),
      array('z_add',true,array('key',2,'member'),7),
      array('z_incr_by',true,array('key',3,'member'),7),
      array('z_rev_range',false,array('key',0,-1),array()),
      array('z_rev_range_with_scores',false,array('key',0,-1),array()),
      array('z_delete',true,array('key','member'),true),
      array('delete',true,array('key'),7),
      array('z_score',false,array('key','member'),7),
      array('z_rev_rank',false,array('key','member'),7),
      array('h_incr_by',true,array('key','field',3),7),
      array('h_get',false,array('key','field'),7),
    );
  }
}
?>
