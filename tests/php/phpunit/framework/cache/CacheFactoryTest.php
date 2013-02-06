<?php
class CacheFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $r = new ReflectionClass('CacheFactory');
    $this->assertTrue($r->implementsInterface('IGetInstance'));
    $cf = CacheFactory::get_instance();
    $this->assertInstanceOf('ICacheFactory',$cf);
  }
  public function testCallsSpawn(){
    $setup = array(
      'whatever',  
    );
    $cache = $this->getMock('ICache');
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(array(
        'caches' => array(
          'default' => $setup ,
        ),
      )));
    $this->set_global_mock('Config',$config);
    $cf = $this->getMock('CacheFactory',array('spawn'));
    $cf
      ->expects($this->once())
      ->method('spawn')
      ->with($this->equalTo($setup))
      ->will($this->returnValue($cache));
    $this->assertSame($cache , $cf->get_cache('default'));
  }
  public function testCallsHelperFactory(){
    $setup = array(
      'type' => 'magic',
      'config' => 'whatever',
    );
    $cache = $this->getMock('ICache');
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(array(
        'caches' => array(
          'default' => $setup ,
        ),
      )));
    $this->set_global_mock('Config',$config);
    $helper_factory = $this->getMock('IConfigurableCacheFactory');
    $cf = $this->getMock('CacheFactory',array('get_factory_by_type'));
    $cf
      ->expects($this->once())
      ->method('get_factory_by_type')
      ->with($this->equalTo('magic'))
      ->will($this->returnValue($helper_factory));
    $helper_factory
      ->expects($this->once())
      ->method('get_cache_from_config')
      //@todo: PHPUnit Mock's have $this not equal to themselves..?
      ->with($this->anything(),$this->equalTo('whatever'))
      ->will($this->returnValue($cache));
    $this->assertSame($cache , $cf->get_cache('default'));
    $this->assertSame($cache , $cf->get_cache('default'));
  }
  
}
?>
