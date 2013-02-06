<?php
class PrefetchingCacheFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $pf = PrefetchingCacheFactory::get_instance();
    $this->assertInstanceOf('IGetInstance',$pf);
    $this->assertInstanceOf('ICacheFactory',$pf);
    $this->assertInstanceOf('IPrefetchingCacheFactory',$pf);
  }
  public function testAddsPrefetching(){
    $cache = $this->getMock('ICache');
    $cache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('x'))
      ->will($this->returnValue('y'));
    $cf = $this->getMock('ICacheFactory');
    $cf
      ->expects($this->once())
      ->method('get_cache')
      ->with($this->equalTo('magic'))
      ->will($this->returnValue($cache));
    $pf = $this->getMock('PrefetchingCacheFactory',array('get_cache_factory'));
    $pf
      ->expects($this->once())
      ->method('get_cache_factory')
      ->will($this->returnValue($cf));
    $wrapped = $pf->get_cache('magic');
    $this->assertNotSame($cache,$wrapped);
    $this->assertInstanceOf('IPrefetchingCache',$wrapped);
    $this->assertSame('y',$wrapped->get('x'));
  }
}
?>
