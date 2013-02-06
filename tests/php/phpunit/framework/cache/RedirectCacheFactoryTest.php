<?php
class RedirectCacheFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $r = RedirectCacheFactory::get_instance();
    $this->assertInstanceOf('IGetInstance',$r);
    $this->assertInstanceOf('IConfigurableCacheFactory',$r);
    $cache = $this->getMock('ICache');
    $cf = $this->getMock('ICacheFactory');
    $cf
      ->expects($this->once())
      ->method('get_cache')
      ->with($this->equalTo('x'))
      ->will($this->returnValue($cache));
    $this->assertSame($cache,$r->get_cache_from_config($cf,'x'));
  }
}
?>
