<?php
class RedisFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new RedisFactory();
  }
  public function testInterface(){
    $factory = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$factory);
    $this->assertInstanceOf('IRedisFactory',$factory);
  }
  public function testCreation(){
    $name = 'whatever';
    $config = array(
      'something'=>'opaque',
    );
    $this->setConfig(array(
      'redises'=>array(
        $name => array(
          'type'=>'redis',
          'config'=>$config,
        ),
      ),
    ));
    $r = $this->getMock('IRedisDB');
    $f = $this->getMock('IConfigurableRedisFactory');
    $factory = $this->getMock('RedisFactory',array('get_factory_by_type'));
    $factory
      ->expects($this->once())
      ->method('get_factory_by_type')
      ->with($this->equalTo('redis'))
      ->will($this->returnValue($f));
    $f
      ->expects($this->once())
      ->method('get_redis_from_config')
      //@todo this->equalTo($factory) or even identicalTo()
      ->with($this->isInstanceOf('IRedisFactory'),$this->equalTo($config))
      ->will($this->returnValue($r));
    $this->assertSame($r,$factory->get_redis($name));
  }
  /**
   * @group redis
   */
  public function testGetRedis(){
    $name = 'whatever';
    $config = array(
      'host'=>'127.0.0.1',
      'port'=>6379,
      'ttl'=>2.5,
      'persistent'=>false,
    );
    $this->setConfig(array(
      'redises'=>array(
        $name => array(
          'type'=>'redis',
          'config'=>$config,
        ),
      ),
    ));
    $f = $this->getSUT();
    $this->assertInstanceOf('IRedisDB',$f->get_redis($name));
  }


}
?>
