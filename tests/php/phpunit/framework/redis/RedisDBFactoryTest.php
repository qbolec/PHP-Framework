<?php
class RedisDBFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new RedisDBFactory();
  }
  public function testInterface(){
    $f = $this->getSUT();
    $this->assertInstanceOf('IConfigurableRedisFactory',$f);
  }
  public function testCreation(){
    $f = $this->getSUT();
    $factory = $this->getMock('IRedisFactory');
    $config = array(
      'host' => '127.0.0.1',
      'port' => 80,//tak, to zły port, ale redis tylko się łączy i nic nie wysyła więc nie zauważy
      'ttl' => 2.4,
      'persistent' => false,
    );
    $r = $f->get_redis_from_config($factory,$config);
    $this->assertInstanceOf('IRedisDB',$r);
  }
}
?>
