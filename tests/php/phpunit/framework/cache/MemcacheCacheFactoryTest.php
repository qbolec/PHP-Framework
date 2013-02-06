<?php
class MemcacheCacheFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $f = MemcacheCacheFactory::get_instance();
    $this->assertInstanceOf('IGetInstance',$f);
    $this->assertInstanceOf('IConfigurableCacheFactory',$f);

  }
  public function testSpawns(){
    $servers=array(
      array(
        'host'=>'localhost',
        'port'=>11211,
      ),
    );
    $cache = $this->getMock('ICache');
    $ttl = 69;
    $f = $this->getMock('MemcacheCacheFactory',array('spawn'));
    $f
      ->expects($this->once())
      ->method('spawn')
      ->with($this->equalTo($servers),$this->equalTo($ttl))
      ->will($this->returnValue($cache));

    $config = $this->getMock('IConfig');
    $config
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('memcaches/clusters/fak/servers'))
      ->will($this->returnValue($servers));
    $this->set_global_mock('Config',$config);

    $cf = $this->getMock('ICacheFactory');
    $this->assertSame($cache,$f->get_cache_from_config($cf,array(
      'ttl'=>$ttl,
      'cluster'=>'fak',
    )));
  }
}
?>
