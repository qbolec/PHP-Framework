<?php
class RedisExTest extends FrameworkTestCase
{
  public function testInterface(){
    $redis = $this->getMock('RedisEx',array('ping'));
    $this->assertInstanceOf('IRedis',$redis);
    $this->assertInstanceOf('Redis',$redis);
  }
}
?>
