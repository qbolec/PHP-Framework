<?php
class LayeredCacheFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $f = LayeredCacheFactory::get_instance();
    $this->assertInstanceOf('IGetInstance',$f);
    $this->assertInstanceOf('IConfigurableCacheFactory',$f);
    $near = $this->getMock('ICache');
    $near
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('x'))
      ->will($this->returnValue(true));
    
    $far = $this->getMock('ICache');
    $far
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('x'),$this->equalTo(8))
      ->will($this->returnValue(false));

    $cf = $this->getMock('ICacheFactory');
    $cf
      ->expects($this->exactly(2))
      ->method('get_cache')
      ->will($this->returnCallback(function($name)use($near,$far){
        switch($name){
        case 'n':return $near;
        case 'f':return $far;
        }
        throw new InvalidArgumentException($name);
      }));
    $cache=$f->get_cache_from_config($cf,array('near'=>'n','far'=>'f'));
    $this->assertInstanceOf('LayeredCache',$cache);
    $this->assertSame(false,$cache->add('x',8)); 
  }
}
?>
