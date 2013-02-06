<?php
class ArrayCacheFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = ArrayCacheFactory::get_instance();
    $this->assertInstanceOf('IGetInstance',$a);
    $this->assertInstanceOf('IConfigurableCacheFactory',$a);
    $cf = $this->getMock('ICacheFactory');
    $this->assertInstanceOf('ArrayCache',$a->get_cache_from_config($cf,null));
  }
}
?>
